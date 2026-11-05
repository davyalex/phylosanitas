<?php

namespace App\Services;

use App\Models\Etablissement;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Import des pharmacies de garde (rempli chaque semaine par une tâche planifiée Claude).
 *
 * Sources acceptées (réglage « gardes_csv_url ») :
 *   - un dossier Google Drive : le fichier le plus récent est importé (compte Google lié au site,
 *     voir GoogleDrive : le dossier peut rester privé) ;
 *   - un fichier Google Drive ou une feuille Google Sheets (compte lié, ou partagés « avec le lien ») ;
 *   - une feuille publiée sur le Web au format CSV, ou toute adresse https d'un CSV.
 * Un fichier peut aussi être envoyé à la main depuis l'administration.
 *
 * Formats lus : Google Sheets, CSV (virgule, point-virgule, tabulation), Excel .xlsx,
 * Google Docs ou HTML contenant un tableau, JSON.
 *
 * Colonnes reconnues (ligne d'en-tête, ordre libre, majuscules et accents ignorés) :
 *   ville | commune | pharmacie | telephone | debut | fin   (ou « periode » : « du … au … »)
 *   facultatives : adresse | latitude | longitude | itineraire (lien Google Maps)
 *   Le nom du pharmacien, s'il est présent, est ignoré (donnée personnelle, jamais publiée).
 * Sans colonnes de dates, la période est cherchée dans le titre du fichier ou au-dessus du tableau
 * (ex. « pharmacies_de_garde_CI_2026-10-31_au_11-06.csv »).
 *
 * Sécurités avant publication : au moins MIN_LIGNES pharmacies valides, dates cohérentes,
 * garde qui couvre aujourd'hui ou commence dans les 7 jours. Sinon la liste en place est
 * conservée et l'erreur est affichée dans l'administration.
 */
class GardesImport
{
    public const MIN_LIGNES    = 20;
    private const DELAI_AUTO   = 6 * 3600;        // vérification automatique au plus toutes les 6 h
    private const VERROU       = 'gardes_import_verrou';
    private const TAILLE_MAX   = 5 * 1024 * 1024;  // 5 Mo
    private const DRIVE_API    = 'https://www.googleapis.com/drive/v3/files';

    /** Types de fichiers Drive lisibles => format interne */
    private const MIMES = [
        'application/vnd.google-apps.spreadsheet' => 'gsheet',
        'application/vnd.google-apps.document'    => 'gdoc',
        'text/csv'                                => 'csv',
        'text/plain'                              => 'csv',
        'text/tab-separated-values'               => 'csv',
        'application/vnd.ms-excel'                => 'csv',   // CSV enregistré par Excel
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        'application/json'                        => 'json',
        'text/html'                               => 'html',
    ];

    /**
     * Colonnes reconnues : nom interne => variantes d'en-tête acceptées, par ordre de préférence
     * (« Commune / Secteur » passe avant une colonne « Zone » Abidjan / Intérieur).
     */
    private const COLONNES = [
        'ville'      => ['ville', 'city', 'localite'],
        'commune'    => ['commune', 'commune secteur', 'commune quartier', 'commune et quartier', 'quartier', 'secteur', 'zone'],
        'pharmacie'  => ['pharmacie', 'nom', 'pharmacie de garde', 'nom de la pharmacie', 'pharmacies'],
        'telephone'  => ['telephone', 'tel', 'contact', 'numero', 'contacts', 'telephones'],
        'debut'      => ['debut', 'garde debut', 'date debut', 'du', 'date de debut'],
        'fin'        => ['fin', 'garde fin', 'date fin', 'au', 'date de fin'],
        'periode'    => ['periode', 'semaine', 'periode de garde', 'dates', 'date'],
        'adresse'    => ['adresse', 'adresse repere', 'adresse et repere', 'repere', 'situation geographique', 'situation', 'localisation', 'emplacement'],
        'latitude'   => ['latitude', 'lat'],
        'longitude'  => ['longitude', 'lng', 'lon', 'long'],
        'maps'       => ['itineraire google maps', 'itineraire', 'lien itineraire', 'google maps', 'lien google maps', 'lien maps', 'maps', 'carte'],
    ];

    private const MOIS = [
        'janvier' => 1, 'fevrier' => 2, 'mars' => 3, 'avril' => 4, 'mai' => 5, 'juin' => 6, 'juillet' => 7,
        'aout' => 8, 'septembre' => 9, 'octobre' => 10, 'novembre' => 11, 'decembre' => 12,
    ];

    /* ====================== Réglages ====================== */

    /** Lien de la source : dossier Drive, fichier Drive, feuille Google Sheets ou CSV publié */
    /** Lien saisi dans l'administration, sinon le dossier GOOGLE_DRIVE_DOSSIER_ID du .env */
    public static function url(): ?string
    {
        if ($url = Setting::get('gardes_csv_url')) return $url;

        $id = trim((string) config('sante.drive.dossier'));

        return $id !== '' ? 'https://drive.google.com/drive/folders/' . $id : null;
    }

    /** Vrai si le dossier vient du .env (aucun lien saisi dans l'administration) */
    public static function urlDepuisEnv(): bool
    {
        return !Setting::get('gardes_csv_url') && self::url();
    }

    public static function autoActif(): bool
    {
        return (bool) (int) Setting::get('gardes_import_auto', 0) && self::url();
    }

    /**
     * Nature du lien : ['type' => dossier|fichier|feuille|csv, 'id' => …]
     * Accepte les liens copiés depuis Drive (« Partager > Copier le lien »), avec ou sans ?usp=…
     */
    public static function analyserLien(?string $url): ?array
    {
        if (!$url) return null;

        if (preg_match('#drive\.google\.com/(?:drive/(?:u/\d+/)?)?folders/([\w-]{10,})#', $url, $m)) return ['type' => 'dossier', 'id' => $m[1]];
        if (preg_match('#docs\.google\.com/spreadsheets/d/e/#', $url))                                   return ['type' => 'csv', 'id' => null];
        if (preg_match('#docs\.google\.com/spreadsheets/d/([\w-]{20,})#', $url, $m)) {
            // « gid » = onglet de la feuille (sinon le premier)
            return ['type' => 'feuille', 'id' => $m[1], 'gid' => preg_match('/[#?&]gid=(\d+)/', $url, $g) ? $g[1] : null];
        }
        if (preg_match('#docs\.google\.com/document/d/([\w-]{20,})#', $url, $m))                           return ['type' => 'fichier', 'id' => $m[1]];
        if (preg_match('#drive\.google\.com/(?:file/d/|open\?id=|uc\?(?:.*&)?id=)([\w-]{10,})#', $url, $m)) return ['type' => 'fichier', 'id' => $m[1]];

        return ['type' => 'csv', 'id' => null];
    }

    /** Dernier import : ['at', 'ok', 'message', 'origine', 'nb', 'debut', 'fin', 'fichier'] */
    public static function dernier(): ?array
    {
        $raw = Setting::get('gardes_import_log');

        return $raw ? json_decode($raw, true) : null;
    }

    /**
     * Appelé à chaque page du site : si l'import automatique est actif et que la dernière
     * vérification date de plus de 6 h, on relance l'import après l'envoi de la page.
     */
    public static function verifierEnArrierePlan(): void
    {
        try {
            if (!self::autoActif()) return;
            if (Cache::has('gardes_import_recent')) return;

            Cache::put('gardes_import_recent', true, self::DELAI_AUTO);
            dispatch(fn() => self::importer('auto'))->afterResponse();
        } catch (\Throwable) {
            // l'affichage du site ne doit jamais dépendre de l'import
        }
    }

    /* ====================== Import ====================== */

    /**
     * Import depuis la source configurée.
     * $origine : auto (visites du site), commande (artisan / cron), manuel (bouton de l'administration).
     * En auto et en commande, un fichier déjà vu n'est pas réimporté : un fichier envoyé à la main
     * reste donc en ligne jusqu'à ce qu'un nouveau fichier arrive dans Drive.
     */
    public static function importer(string $origine = 'manuel'): array
    {
        return self::verrouille($origine, function () use ($origine) {
            $url = self::url();
            if (!$url) return self::journal(false, 'Aucun lien Google Drive configuré.', $origine);

            $doc = self::telecharger($url);

            if ($origine !== 'manuel' && Setting::get('gardes_import_version') === $doc['version']) {
                return self::journal(true, 'Aucun nouveau fichier depuis le dernier import.', $origine, 0, null, null, $doc['nom'], false);
            }

            $r = self::traiter($doc['contenu'], $doc['format'], $doc['nom'], $origine);
            // Fichier lu : on ne le relira pas automatiquement, même refusé (il faudrait un nouveau fichier)
            Setting::putMany(['gardes_import_version' => $doc['version']]);

            return $r;
        });
    }

    /** Import d'un fichier envoyé depuis l'administration */
    public static function importerFichier(string $contenu, string $nomFichier): array
    {
        return self::verrouille('fichier', function () use ($contenu, $nomFichier) {
            return self::traiter($contenu, self::formatDepuisNom($nomFichier, $contenu), $nomFichier, 'fichier');
        });
    }

    private static function verrouille(string $origine, callable $fn): array
    {
        $lock = Cache::lock(self::VERROU, 120);
        if (!$lock->get()) {
            return ['ok' => false, 'message' => 'Un import est déjà en cours, réessayez dans une minute.'];
        }

        try {
            return $fn();
        } catch (\Throwable $e) {
            $msg = $e->getMessage();
            Log::warning('Import des gardes impossible', ['erreur' => $msg]);
            return self::journal(false, $e instanceof ImportGardesException ? $msg : 'Erreur pendant l\'import : ' . Str::limit($msg, 160), $origine);
        } finally {
            $lock->release();
        }
    }

    /** Contenu brut → contrôles → publication */
    private static function traiter(string $contenu, string $format, string $nom, string $origine): array
    {
        [$lignes, $erreurs] = self::lire($contenu, $format, $nom);

        if (count($lignes) < self::MIN_LIGNES) {
            return self::journal(false, count($lignes) . ' pharmacie(s) valide(s) seulement (minimum ' . self::MIN_LIGNES . ') : liste non publiée, l\'ancienne est conservée.'
                . ($erreurs ? ' Problème : ' . $erreurs[0] : ''), $origine, 0, null, null, $nom);
        }

        $debut = collect($lignes)->min('debut');
        $fin   = collect($lignes)->max('fin');
        $today = now()->startOfDay();

        if (Carbon::parse($fin)->lt($today)) {
            return self::journal(false, 'La liste est périmée (garde terminée le ' . Carbon::parse($fin)->format('d/m/Y') . ') : la tâche planifiée n\'a sans doute pas tourné.', $origine, 0, null, null, $nom);
        }
        if (Carbon::parse($debut)->gt($today->copy()->addDays(7))) {
            return self::journal(false, 'La garde commence dans plus de 7 jours (' . Carbon::parse($debut)->format('d/m/Y') . ') : vérifiez les dates.', $origine, 0, null, null, $nom);
        }

        [$crees, $majs] = self::publier($lignes);

        return self::journal(true, count($lignes) . ' pharmacies de garde publiées (' . $crees . ' nouvelle(s), ' . $majs . ' mise(s) à jour).'
            . ($erreurs ? ' ' . count($erreurs) . ' ligne(s) ignorée(s).' : ''), $origine, count($lignes), $debut, $fin, $nom);
    }

    /* ====================== Téléchargement ====================== */

    /** Retourne ['contenu', 'format', 'nom', 'version'] */
    private static function telecharger(string $url): array
    {
        $lien  = self::analyserLien($url);
        $drive = GoogleDrive::connecte();

        switch ($lien['type']) {
            case 'dossier':
                if (!$drive) throw new ImportGardesException(GoogleDrive::erreurService() ?: 'Pour lire un dossier, configurez le compte de service Google (voir DOCUMENTATION.md) ou cliquez sur « Connecter Google Drive ».');
                return self::telechargerFichierDrive(self::dernierFichierDuDossier($lien['id']));

            case 'fichier':
            case 'feuille':
                if ($drive) {
                    $f = GoogleDrive::get(self::DRIVE_API . '/' . $lien['id'], ['fields' => 'id,name,mimeType,modifiedTime,size', 'supportsAllDrives' => 'true'])->json();
                    if (!empty($lien['gid'])) {
                        // Onglet précis : export CSV de cet onglet avec le compte lié
                        $res = GoogleDrive::get('https://docs.google.com/spreadsheets/d/' . $lien['id'] . '/export', ['format' => 'csv', 'gid' => $lien['gid']]);
                        return ['contenu' => $res->body(), 'format' => 'csv', 'nom' => $f['name'] ?? 'feuille Google Sheets', 'version' => $f['id'] . '#' . $lien['gid'] . '@' . ($f['modifiedTime'] ?? md5($res->body()))];
                    }
                    return self::telechargerFichierDrive($f);
                }
                // Sans connexion : liens publics de Google (fichier partagé « Tous les utilisateurs disposant du lien »)
                $direct = $lien['type'] === 'feuille'
                    ? 'https://docs.google.com/spreadsheets/d/' . $lien['id'] . '/export?format=csv' . (!empty($lien['gid']) ? '&gid=' . $lien['gid'] : '')
                    : (str_contains($url, 'docs.google.com/document')
                        ? 'https://docs.google.com/document/d/' . $lien['id'] . '/export?format=html'
                        : 'https://drive.usercontent.google.com/download?export=download&id=' . $lien['id']);
                $res = self::get($direct);
                $nom = self::nomDepuisEntetes($res) ?: 'fichier Google Drive';
                return ['contenu' => $res->body(), 'format' => $lien['type'] === 'feuille' ? 'csv' : self::formatDepuisNom($nom, $res->body(), $res->header('Content-Type')), 'nom' => $nom, 'version' => md5($res->body())];

            default:
                $res = self::get($url);
                return ['contenu' => $res->body(), 'format' => self::formatDepuisNom($url, $res->body(), $res->header('Content-Type')), 'nom' => 'feuille publiée', 'version' => md5($res->body())];
        }
    }

    /** Fichier lisible le plus récemment modifié du dossier */
    private static function dernierFichierDuDossier(string $dossier): array
    {
        $res = GoogleDrive::get(self::DRIVE_API, [
            'q'        => "'{$dossier}' in parents and trashed = false",
            'orderBy'  => 'modifiedTime desc',
            'pageSize' => 50,
            'fields'   => 'files(id,name,mimeType,modifiedTime,size)',
            'supportsAllDrives' => 'true',
            'includeItemsFromAllDrives' => 'true',
        ]);

        $fichiers = collect($res->json('files', []));
        if ($fichiers->isEmpty()) {
            throw new ImportGardesException(GoogleDrive::modeService()
                ? 'Le dossier Google Drive est vide, ou il n\'est pas partagé avec ' . GoogleDrive::compte() . ' (Partager > ajouter cette adresse en « Lecteur »).'
                : 'Le dossier Google Drive est vide (ou le compte ' . GoogleDrive::compte() . ' n\'y a pas accès).');
        }

        $f = $fichiers->first(fn($f) => isset(self::MIMES[$f['mimeType'] ?? ''])
            || in_array(strtolower(pathinfo($f['name'] ?? '', PATHINFO_EXTENSION)), ['csv', 'xlsx', 'json', 'txt', 'tsv'], true));

        if (!$f) {
            throw new ImportGardesException('Aucun fichier lisible dans le dossier (Google Sheets, Google Docs, CSV, Excel .xlsx ou JSON). Dernier fichier : « ' . $fichiers->first()['name'] . ' ».');
        }

        return $f;
    }

    private static function telechargerFichierDrive(array $f): array
    {
        if (!empty($f['size']) && (int) $f['size'] > self::TAILLE_MAX) {
            throw new ImportGardesException('Le fichier « ' . $f['name'] . ' » est trop lourd (plus de 5 Mo).');
        }

        $mime = $f['mimeType'] ?? '';
        $url  = self::DRIVE_API . '/' . $f['id'];

        if ($mime === 'application/vnd.google-apps.spreadsheet') {
            $res = GoogleDrive::get($url . '/export', ['mimeType' => 'text/csv']);
            $format = 'csv';
        } elseif ($mime === 'application/vnd.google-apps.document') {
            $res = GoogleDrive::get($url . '/export', ['mimeType' => 'text/html']);
            $format = 'html';
        } elseif (str_starts_with($mime, 'application/vnd.google-apps.')) {
            throw new ImportGardesException('Le fichier « ' . $f['name'] . ' » n\'est ni une feuille Google Sheets ni un document Google Docs.');
        } else {
            $res = GoogleDrive::get($url, ['alt' => 'media', 'supportsAllDrives' => 'true']);
            $format = self::MIMES[$mime] ?? self::formatDepuisNom($f['name'], $res->body());
            if ($format === 'csv' || $format === 'html') $format = self::formatDepuisNom($f['name'], $res->body(), $mime);
        }

        return [
            'contenu' => $res->body(),
            'format'  => $format,
            'nom'     => $f['name'] ?? 'fichier Google Drive',
            'version' => ($f['id'] ?? '') . '@' . ($f['modifiedTime'] ?? md5($res->body())),
        ];
    }

    private static function get(string $url): \Illuminate\Http\Client\Response
    {
        $res = Http::timeout(25)->retry(2, 1000, null, false)->get($url);

        if (!$res->successful()) {
            throw new ImportGardesException('Le fichier est inaccessible (code ' . $res->status() . '). Vérifiez qu\'il est partagé « Tous les utilisateurs disposant du lien » ou publié sur le Web.');
        }
        if (strlen($res->body()) > self::TAILLE_MAX) throw new ImportGardesException('Le fichier est trop lourd (plus de 5 Mo).');
        // Page de connexion Google au lieu du fichier : document privé
        if (str_contains(substr($res->body(), 0, 3000), 'accounts.google.com')) {
            throw new ImportGardesException('Google demande une connexion : le fichier n\'est pas partagé « Tous les utilisateurs disposant du lien ».');
        }

        return $res;
    }

    private static function nomDepuisEntetes(\Illuminate\Http\Client\Response $res): ?string
    {
        $cd = $res->header('Content-Disposition');
        if (preg_match("/filename\*=UTF-8''([^;]+)/i", $cd, $m)) return rawurldecode($m[1]);
        if (preg_match('/filename="?([^";]+)"?/i', $cd, $m)) return $m[1];

        return null;
    }

    private static function formatDepuisNom(string $nom, string $contenu, ?string $mime = null): string
    {
        $ext = strtolower(pathinfo(parse_url($nom, PHP_URL_PATH) ?: $nom, PATHINFO_EXTENSION));
        if ($ext === 'xlsx' || str_starts_with($contenu, "PK\x03\x04")) return 'xlsx';
        if ($ext === 'json' || ($mime && str_contains($mime, 'json'))) return 'json';
        $debut = ltrim(substr($contenu, 0, 500), "\xEF\xBB\xBF \r\n\t");
        if (in_array($ext, ['html', 'htm'], true) || ($mime && str_contains($mime, 'html')) || stripos($debut, '<!doctype html') === 0 || stripos($debut, '<html') === 0) return 'html';
        if (str_starts_with($debut, '[') || str_starts_with($debut, '{')) return 'json';

        return 'csv';
    }

    /* ====================== Lecture ====================== */

    /** Contenu → [lignes valides, erreurs] */
    public static function lire(string $contenu, string $format = 'csv', string $nom = ''): array
    {
        try {
            [$rows, $contexte] = match ($format) {
                'xlsx'  => [self::lignesXlsx($contenu), ''],
                'json'  => self::lignesJson($contenu),
                'html'  => self::lignesHtml($contenu),
                default => [self::lignesCsv($contenu), ''],
            };
        } catch (ImportGardesException $e) {
            return [[], [$e->getMessage()]];
        }

        return self::analyser($rows, trim($nom . ' ' . $contexte));
    }

    /**
     * Tableau de cellules → pharmacies.
     * Repère la ligne d'en-tête (pas forcément la première), accepte plusieurs tableaux à la suite
     * (ex. Abidjan puis intérieur) ; les lignes hors tableau servent à trouver la période.
     */
    private static function analyser(array $rows, string $contexte): array
    {
        $index = null;
        $donnees = [];
        $texte = [$contexte];

        foreach ($rows as $n => $cells) {
            $cells = array_map(fn($v) => self::propre((string) $v), $cells);
            if (!array_filter($cells, fn($v) => $v !== '')) continue;

            if ($map = self::entete($cells)) { $index = $map; continue; }
            if ($index === null) { $texte[] = implode(' ', $cells); continue; }
            $donnees[] = [$n + 1, $cells, $index];
        }

        if (!$donnees) {
            return [[], [$index === null ? 'Ligne d\'en-tête introuvable : il faut au moins une colonne « pharmacie » et une colonne « commune », « ville » ou « telephone ».' : 'Aucune pharmacie sous la ligne d\'en-tête.']];
        }

        // Période du document (titre, nom du fichier, lignes au-dessus du tableau)
        $periodeDoc = self::periode(implode(' ', $texte));

        $lignes = $erreurs = [];
        foreach ($donnees as [$num, $c, $index]) {
            $val = fn($k) => isset($index[$k]) ? trim((string) ($c[$index[$k]] ?? '')) : '';

            $nom = self::titre($val('pharmacie'));
            // Abréviations courantes (« Phcie Magnificat », « Pharm. du Plateau ») : même fiche que « Pharmacie … »
            $nom = trim(preg_replace('/^(?:ph(?:ar)?cie|phie|pcie|pharm\.|ph\.)\s*/iu', 'Pharmacie ', $nom));
            if ($nom === '' || mb_strlen($nom) < 3) continue;
            if (!Str::of($nom)->ascii()->lower()->startsWith('pharmacie')) $nom = 'Pharmacie ' . $nom;

            $debut = self::date($val('debut'));
            $fin   = self::date($val('fin'));
            if ((!$debut || !$fin) && ($p = self::periode($val('periode')) ?: $periodeDoc)) {
                $debut = $debut ?: $p[0];
                $fin   = $fin ?: $p[1];
            }
            if (!$debut || !$fin || $fin < $debut) {
                $erreurs[] = 'ligne ' . $num . ' : dates introuvables ou invalides (ajoutez les colonnes « debut » et « fin », ou la période « du … au … » dans le titre)';
                continue;
            }

            $ville    = Str::limit(self::titre($val('ville') ?: 'Abidjan'), 100, '');
            $quartier = Str::limit(self::titre($val('commune')), 100, '') ?: null;
            if ($quartier && self::cle($quartier) === self::cle($ville)) $quartier = null;   // intérieur : « Soubré / Soubré »
            [$lat, $lng] = self::coordonnees($val('latitude'), $val('longitude'));

            // Adresse qui répète seulement la commune ou la ville (« Abobo ») : inutile à l'affichage
            $adresse = Str::limit($val('adresse'), 255, '') ?: null;
            if ($adresse && in_array(self::cle($adresse), [self::cle((string) $quartier), self::cle($ville)], true)) $adresse = null;

            // Sans colonnes GPS : coordonnées lues dans le lien (« ?q=5.43835,-4.03056 », « destination=… », « @5.4,-4.0 »)
            $maps = $val('maps');
            if (!$lat && preg_match('/(?:[?&](?:q|query|destination|daddr|ll|center)=|@)(-?\d{1,2}(?:\.\d+)?)(?:,|%2C)\s*(-?\d{1,3}(?:\.\d+)?)/i', $maps, $g)) {
                [$lat, $lng] = self::coordonnees($g[1], $g[2]);
            }

            // Lien d'itinéraire gardé seulement sans GPS, et s'il ne s'agit pas d'une simple recherche par nom (refaite par le site)
            $recherche = str_contains($maps, '/maps/search') || preg_match('/[?&](?:q|query)=/i', $maps);
            $maps = !$lat && !$recherche && preg_match('#^https://[^\s]+$#', $maps) ? Str::limit($maps, 500, '') : null;

            $lignes[] = [
                'nom'        => Str::limit($nom, 150, ''),
                'ville'      => $ville,
                'quartier'   => $quartier,
                'adresse'    => $adresse,
                'telephone'  => self::telephone($val('telephone')),
                'latitude'   => $lat,
                'longitude'  => $lng,
                'maps_url'   => $maps,
                'debut'      => $debut,
                'fin'        => $fin,
            ];
        }

        return [$lignes, $erreurs];
    }

    /** Ligne d'en-tête ? Retourne [colonne interne => position] ou null */
    private static function entete(array $cells): ?array
    {
        $norm = array_map(fn($h) => Str::of($h)->ascii()->lower()->replace(['_', '-', '.', ':', '(', ')', '/', '+'], ' ')->squish()->toString(), $cells);

        // Variante préférée d'abord : « Commune / Secteur » l'emporte sur « Zone » même placée après
        $map = [];
        foreach (self::COLONNES as $cle => $variantes) {
            foreach ($variantes as $variante) {
                $i = array_search($variante, $norm, true);
                if ($i !== false && !in_array($i, $map, true)) { $map[$cle] = $i; break; }
            }
        }

        $autres = array_intersect(array_keys($map), ['ville', 'commune', 'telephone', 'debut', 'fin', 'periode']);

        return isset($map['pharmacie']) && count($autres) >= 1 ? $map : null;
    }

    private static function lignesCsv(string $csv): array
    {
        $csv = preg_replace('/^\xEF\xBB\xBF/', '', $csv);
        if (!mb_check_encoding($csv, 'UTF-8')) $csv = mb_convert_encoding($csv, 'UTF-8', 'Windows-1252');

        // Séparateur : celui qui revient le plus sur les premières lignes
        $extrait = implode("\n", array_slice(preg_split('/\r\n|\n|\r/', $csv), 0, 15));
        $sep = collect([',' => substr_count($extrait, ','), ';' => substr_count($extrait, ';'), "\t" => substr_count($extrait, "\t")])->sort()->keys()->last();

        $f = fopen('php://temp', 'w+');
        fwrite($f, $csv);
        rewind($f);
        $rows = [];
        while (($r = fgetcsv($f, 0, $sep)) !== false) {
            if ($r !== [null]) $rows[] = $r;
        }
        fclose($f);

        return $rows;
    }

    /** Excel .xlsx : première feuille, sans bibliothèque (ZipArchive + XML) */
    private static function lignesXlsx(string $binaire): array
    {
        if (!class_exists(\ZipArchive::class)) throw new ImportGardesException('Le serveur ne sait pas lire les fichiers Excel : enregistrez le fichier en CSV.');

        $tmp = tempnam(sys_get_temp_dir(), 'pdg');
        file_put_contents($tmp, $binaire);
        $zip = new \ZipArchive();

        try {
            if ($zip->open($tmp) !== true) throw new ImportGardesException('Fichier Excel illisible.');

            $partages = [];
            if (($xml = $zip->getFromName('xl/sharedStrings.xml')) !== false) {
                foreach (simplexml_load_string($xml)->si as $si) {
                    $t = isset($si->t) ? (string) $si->t : '';
                    foreach ($si->r as $r) $t .= (string) $r->t;
                    $partages[] = $t;
                }
            }

            $feuille = $zip->getFromName('xl/worksheets/sheet1.xml');
            if ($feuille === false) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    if (preg_match('#^xl/worksheets/[^/]+\.xml$#', $zip->getNameIndex($i))) { $feuille = $zip->getFromIndex($i); break; }
                }
            }
            if (!$feuille) throw new ImportGardesException('Aucune feuille dans le fichier Excel.');

            $rows = [];
            foreach (simplexml_load_string($feuille)->sheetData->row as $row) {
                $ligne = [];
                foreach ($row->c as $c) {
                    $col = 0;
                    foreach (str_split(preg_replace('/\d+/', '', (string) $c['r'])) as $l) $col = $col * 26 + ord($l) - 64;
                    $t = (string) $c['t'];
                    $v = match ($t) {
                        's'         => $partages[(int) $c->v] ?? '',
                        'inlineStr' => (string) $c->is->t . implode('', array_map('strval', iterator_to_array($c->is->r ?? [], false))),
                        default     => (string) $c->v,
                    };
                    $ligne[max(0, $col - 1)] = $v;
                }
                if ($ligne) {
                    $max = max(array_keys($ligne));
                    $rows[] = array_replace(array_fill(0, $max + 1, ''), $ligne);
                }
            }

            return $rows;
        } finally {
            $zip->close();
            @unlink($tmp);
        }
    }

    /** JSON : liste d'objets, ou objet { debut, fin, pharmacies: [...] } */
    private static function lignesJson(string $json): array
    {
        $data = json_decode(preg_replace('/^\xEF\xBB\xBF/', '', $json), true);
        if (!is_array($data)) throw new ImportGardesException('Fichier JSON illisible.');

        $commun = [];
        if (!array_is_list($data)) {
            $liste = collect($data)->first(fn($v) => is_array($v) && array_is_list($v)) ?? [];
            $commun = array_filter($data, fn($v) => is_scalar($v));
            $data = $liste;
        }

        $objets = array_values(array_filter($data, 'is_array'));
        if (!$objets) throw new ImportGardesException('Aucune pharmacie dans le fichier JSON.');

        $cles = array_values(array_unique(array_merge(...array_map('array_keys', $objets))));
        $rows = [$cles];
        foreach ($objets as $o) $rows[] = array_map(fn($k) => is_scalar($o[$k] ?? null) ? (string) $o[$k] : '', $cles);

        // Les champs communs (« debut », « fin », « periode »…) servent de contexte pour la période
        $contexte = implode(' ', array_map(fn($k, $v) => $k . ' ' . $v, array_keys($commun), $commun));
        if (isset($commun['debut'], $commun['fin'])) {
            $d = self::date((string) $commun['debut']);
            $f = self::date((string) $commun['fin']);
            if ($d && $f) $contexte .= ' du ' . $d . ' au ' . $f;
        }

        return [$rows, $contexte];
    }

    /** HTML (export Google Docs) : toutes les lignes des tableaux ; le texte hors tableau donne la période */
    private static function lignesHtml(string $html): array
    {
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML('<?xml encoding="utf-8"?>' . $html);
        libxml_clear_errors();
        $xp = new \DOMXPath($dom);

        $rows = [];
        foreach ($xp->query('//table//tr') as $tr) {
            $rows[] = array_map(fn($td) => $td->textContent, iterator_to_array($xp->query('./th|./td', $tr)));
        }
        if (!$rows) throw new ImportGardesException('Aucun tableau dans le document.');

        $texte = [];
        foreach ($xp->query('//title|//h1|//h2|//h3|//p[not(ancestor::table)]') as $el) {
            $texte[] = $el->textContent;
        }

        return [$rows, implode(' ', array_slice($texte, 0, 20))];
    }

    /* ====================== Publication ====================== */

    /** Publie la garde : crée les pharmacies inconnues, met à jour les dates, retire la garde des autres */
    private static function publier(array $lignes): array
    {
        $crees = $majs = 0;

        DB::transaction(function () use ($lignes, &$crees, &$majs) {
            $pharmacies = Etablissement::withTrashed()->where('type', 'pharmacie')->get();
            // Même nom possible dans deux communes : on compare nom + ville + commune, puis nom + ville
            $cle = fn(...$parts) => Str::of(implode('|', $parts))->ascii()->lower()->replaceMatches('/[^a-z0-9|]+/', '')->toString();
            $parCle = $pharmacies->keyBy(fn($e) => $cle($e->nom, $e->ville, $e->quartier));
            $parNom = $pharmacies->groupBy(fn($e) => $cle($e->nom, $e->ville));
            $gardeIds = [];

            foreach ($lignes as $l) {
                $e = $parCle[$cle($l['nom'], $l['ville'], $l['quartier'])] ?? null;
                if (!$e) {
                    // Fiche saisie à la main sans commune : on la reprend si elle est la seule de ce nom dans la ville
                    $memes = ($parNom[$cle($l['nom'], $l['ville'])] ?? collect())->filter(fn($x) => !in_array($x->id, $gardeIds, true));
                    if ($memes->count() === 1 && !$memes->first()->quartier) $e = $memes->first();
                }

                if (!$e) {
                    $e = Etablissement::create([
                        'nom' => $l['nom'], 'type' => 'pharmacie',
                        'ville' => $l['ville'], 'quartier' => $l['quartier'], 'adresse' => $l['adresse'],
                        'telephone' => $l['telephone'], 'latitude' => $l['latitude'], 'longitude' => $l['longitude'],
                        'maps_url' => $l['maps_url'], 'actif' => true,
                    ]);
                    $parCle[$cle($l['nom'], $l['ville'], $l['quartier'])] = $e;
                    $crees++;
                } else {
                    if ($e->trashed()) $e->restore();
                    $majs++;
                }

                // Les informations saisies à la main restent prioritaires : le fichier ne complète que les cases vides
                $gps = $e->latitude === null && $l['latitude'] !== null;
                $e->forceFill([
                    'garde_debut' => $l['debut'],
                    'garde_fin'   => $l['fin'],
                    'quartier'    => $e->quartier ?: $l['quartier'],
                    'telephone'   => $e->telephone ?: $l['telephone'],
                    'adresse'     => $e->adresse ?: $l['adresse'],
                    'latitude'    => $gps ? $l['latitude'] : $e->latitude,
                    'longitude'   => $gps ? $l['longitude'] : $e->longitude,
                    'maps_url'    => $e->maps_url ?: $l['maps_url'],
                ])->saveQuietly();
                $gardeIds[] = $e->id;
            }

            // Les pharmacies absentes de la nouvelle liste ne sont plus de garde
            Etablissement::where('type', 'pharmacie')->whereNotIn('id', $gardeIds)
                ->whereNotNull('garde_fin')
                ->update(['garde_debut' => null, 'garde_fin' => null]);
        });

        Etablissement::viderCache();

        return [$crees, $majs];
    }

    private static function journal(bool $ok, string $message, string $origine, int $nb = 0, ?string $debut = null, ?string $fin = null, ?string $fichier = null, bool $enregistrer = true): array
    {
        $entree = [
            'at' => now()->toIso8601String(), 'ok' => $ok, 'message' => $message, 'origine' => $origine,
            'nb' => $nb, 'debut' => $debut, 'fin' => $fin, 'fichier' => $fichier,
        ];

        // Une vérification automatique sans changement n'écrase pas le compte rendu du dernier vrai import
        if ($enregistrer || !self::dernier()) {
            Setting::putMany(['gardes_import_log' => json_encode($entree, JSON_UNESCAPED_UNICODE)]);
        }
        if (!$ok) Log::notice('Import des gardes refusé : ' . $message);

        return $entree;
    }

    /* ====================== Nettoyage des valeurs ====================== */

    /** Date seule : AAAA-MM-JJ, JJ/MM/AAAA, « 2 novembre 2026 », date Excel… */
    private static function date(string $v): ?string
    {
        $v = trim($v);
        if ($v === '') return null;

        if (preg_match('/^(\d{4})[-\/](\d{1,2})[-\/](\d{1,2})(?:[T ][\d:.]+Z?)?$/', $v, $m) && checkdate((int) $m[2], (int) $m[3], (int) $m[1])) {
            return sprintf('%04d-%02d-%02d', $m[1], $m[2], $m[3]);
        }
        if (preg_match('/^(\d{1,2})[\/.-](\d{1,2})[\/.-](\d{2}|\d{4})$/', $v, $m)) {
            $an = strlen($m[3]) === 2 ? 2000 + (int) $m[3] : (int) $m[3];
            if (checkdate((int) $m[2], (int) $m[1], $an)) return sprintf('%04d-%02d-%02d', $an, $m[2], $m[1]);
        }
        // Numéro de série Excel (jours depuis le 30/12/1899)
        if (preg_match('/^\d{5}(\.\d+)?$/', $v) && $v >= 40000 && $v <= 60000) {
            return Carbon::create(1899, 12, 30)->addDays((int) $v)->toDateString();
        }
        $p = self::datesDansTexte($v);

        return count($p) === 1 ? $p[0] : null;
    }

    /** Période « du … au … » dans un texte : [debut, fin] ou null */
    private static function periode(string $texte): ?array
    {
        $dates = self::datesDansTexte($texte);
        if (count($dates) < 2) return null;

        [$d, $f] = [$dates[0], $dates[1]];

        return $f >= $d && Carbon::parse($d)->diffInDays(Carbon::parse($f)) <= 31 ? [$d, $f] : null;
    }

    /** Toutes les dates d'un texte, dans l'ordre ; l'année manquante est reprise de la date suivante */
    private static function datesDansTexte(string $texte): array
    {
        $t = Str::of($texte)->ascii()->lower()->toString();

        // « 2026-10-31_au_11-06 » (nom de fichier) : la seconde date, sans année, suit l'ordre mois-jour de la première
        $t = preg_replace_callback('/(\d{4})-(\d{2})-(\d{2})(\D{1,6}?)(\d{2})-(\d{2})(?![-\d])/', function ($m) {
            $an = $m[5] . $m[6] < $m[2] . $m[3] ? (int) $m[1] + 1 : (int) $m[1];   // « 2026-12-29_au_01-04 »
            return $m[1] . '-' . $m[2] . '-' . $m[3] . $m[4] . $an . '-' . $m[5] . '-' . $m[6];
        }, $t);

        $mois = implode('|', array_keys(self::MOIS));

        // « du 2 au 8 novembre 2026 » : le premier jour reprend le mois écrit une seule fois
        $t = preg_replace('/\b(\d{1,2})(?:er)?\s+au\s+(\d{1,2})(?:er)?\s+(' . $mois . ')\b/', '$1 $3 au $2 $3', $t);

        // AAAA-MM-JJ | JJ/MM(/AAAA) | « 2 novembre (2026) » | JJ-MM-AAAA (« semaine du 31-10-2026 au 06-11-2026 »)
        preg_match_all('/(\d{4})-(\d{2})-(\d{2})|\b(\d{1,2})[\/.](\d{1,2})(?:[\/.](\d{2,4}))?\b|\b(\d{1,2})(?:er)?\s+(' . $mois . ')(?:\s+(\d{4}))?|\b(\d{1,2})-(\d{1,2})-(\d{4})\b/', $t, $all, PREG_SET_ORDER);

        $brut = [];
        foreach ($all as $m) {
            if (!empty($m[1]))      $brut[] = [(int) $m[1], (int) $m[2], (int) $m[3]];
            elseif (!empty($m[4]))  $brut[] = [isset($m[6]) && $m[6] !== '' ? (strlen($m[6]) === 2 ? 2000 + (int) $m[6] : (int) $m[6]) : null, (int) $m[5], (int) $m[4]];
            elseif (!empty($m[10])) $brut[] = [(int) $m[12], (int) $m[11], (int) $m[10]];
            else                    $brut[] = [isset($m[9]) && $m[9] !== '' ? (int) $m[9] : null, self::MOIS[$m[8]], (int) $m[7]];
        }

        // Années manquantes : celle de la date suivante (« du 31 octobre au 6 novembre 2026 »), sinon l'année en cours
        $suivante = null;
        for ($i = count($brut) - 1; $i >= 0; $i--) {
            if ($brut[$i][0] === null) {
                $an = $suivante ? $suivante[0] : (int) now()->year;
                if ($suivante && ($brut[$i][1] > $suivante[1])) $an--;   // « du 29 décembre au 4 janvier 2027 »
                $brut[$i][0] = $an;
            }
            $suivante = $brut[$i];
        }

        $dates = [];
        foreach ($brut as [$a, $mo, $j]) {
            if (checkdate($mo, $j, $a)) $dates[] = sprintf('%04d-%02d-%02d', $a, $mo, $j);
        }

        return $dates;
    }

    private static function telephone(string $v): ?string
    {
        // Garde le premier numéro (8 à 10 chiffres), présenté par groupes de 2 : « 27 22 41 00 01 »
        if (!preg_match('/(\+?225[\s.]?)?((?:\d[\s.]?){8,10})/', $v, $m)) return null;
        $chiffres = preg_replace('/\D/', '', $m[2]);
        // Numéro lu comme un nombre par Excel : le 0 initial a disparu (07… → 7…)
        if (strlen($chiffres) === 9) $chiffres = '0' . $chiffres;

        return trim(chunk_split($chiffres, 2, ' '));
    }

    /** Latitude / longitude (virgule ou point décimal) : [lat, lng] si plausibles pour la Côte d'Ivoire et ses voisins, sinon [null, null] */
    private static function coordonnees(string $lat, string $lng): array
    {
        $lat = str_replace(',', '.', trim($lat));
        $lng = str_replace(',', '.', trim($lng));
        if (!is_numeric($lat) || !is_numeric($lng)) return [null, null];

        $lat = round((float) $lat, 7);
        $lng = round((float) $lng, 7);

        return $lat >= 2 && $lat <= 12 && $lng >= -10 && $lng <= 0 ? [$lat, $lng] : [null, null];
    }

    /** Clé de comparaison : sans accents, majuscules ni ponctuation */
    private static function cle(string $v): string
    {
        return Str::of($v)->ascii()->lower()->replaceMatches('/[^a-z0-9]+/', '')->toString();
    }

    private static function propre(string $v): string
    {
        return trim(preg_replace('/\s+/u', ' ', str_replace("\u{00A0}", ' ', $v)));
    }

    /** « PHARMACIE DU MARCHE » → « Pharmacie Du Marche » ; garde les sigles courts (ST, CHU…) */
    private static function titre(string $v): string
    {
        $v = self::propre($v);
        if ($v === '' || $v !== mb_strtoupper($v)) return $v;

        return mb_convert_case(mb_strtolower($v), MB_CASE_TITLE, 'UTF-8');
    }
}


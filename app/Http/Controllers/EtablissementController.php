<?php

namespace App\Http\Controllers;

use App\Models\Etablissement;
use App\Services\GardesImport;
use App\Services\GoogleDrive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

/** Annuaire santé (pharmacies de garde, hôpitaux, numéros d'urgence…) */
class EtablissementController extends Controller
{
    /* ====================== Site public ====================== */

    public function annuaire(Request $request)
    {
        abort_unless(config('sante.modules.annuaire'), 404);

        $type  = array_key_exists($request->type, Etablissement::TYPES) ? $request->type : null;
        $ville = $request->filled('ville') ? (string) $request->ville : null;
        $q     = trim((string) $request->q);

        $urgences = Etablissement::actifs()->where('type', 'urgence')->orderBy('id')->get();

        $deGarde = Etablissement::actifs()->deGarde()
            ->when($ville, fn($w) => $w->where('ville', $ville))
            ->orderBy('ville')->orderBy('quartier')->orderBy('nom')
            ->get();

        $etablissements = Etablissement::actifs()
            ->where('type', '!=', 'urgence')
            ->when($type, fn($w) => $w->where('type', $type))
            ->when($ville, fn($w) => $w->where('ville', $ville))
            ->when($q !== '', fn($w) => $w->where(fn($s) => $s
                ->where('nom', 'LIKE', "%{$q}%")
                ->orWhere('quartier', 'LIKE', "%{$q}%")
                ->orWhere('adresse', 'LIKE', "%{$q}%")))
            ->orderBy('ville')->orderBy('nom')
            ->paginate(24)
            ->withQueryString();

        $villes = Etablissement::actifs()->whereNotNull('ville')->where('ville', '!=', '')
            ->distinct()->orderBy('ville')->pluck('ville');

        $parType = Etablissement::actifs()->where('type', '!=', 'urgence')
            ->selectRaw('type, count(*) as n')->groupBy('type')->pluck('n', 'type');

        return view('site.pages.annuaire', compact('urgences', 'deGarde', 'etablissements', 'villes', 'parType', 'type', 'ville', 'q'));
    }

    /** Page dédiée aux pharmacies de garde (la plus consultée) : /pharmacies-de-garde?ville=… */
    public function pharmaciesDeGarde(Request $request)
    {
        $toutes = Etablissement::gardesDuJour();
        $villes = $toutes->pluck('ville')->filter()->unique()->sort()->values();

        // Ville choisie : celle demandée, sinon la dernière consultée par ce visiteur
        // (« ?ville= » vide = toutes les villes, choix lui aussi retenu)
        $ville = $request->has('ville') ? $request->query('ville') : $request->cookie('ps_ville_garde');
        if ($ville && !$villes->contains($ville)) $ville = null;

        $gardes = $ville ? $toutes->where('ville', $ville)->values() : $toutes;
        // Abidjan d'abord (par commune), puis les villes de l'intérieur (par ville)
        $parQuartier = $gardes
            // Séparateur « \x01 » (avant tout caractère) : « Abobo » passe avant « Abobo Pk 18 »
            ->sortBy(fn($e) => ($e->ville === 'Abidjan' ? '0' : '1') . implode("\x01", array_map(fn($v) => Str::lower(Str::ascii((string) $v)), [$e->ville, $e->quartier, $e->nom])))
            ->groupBy(fn($e) => $e->ville === 'Abidjan' || !$e->ville ? ($e->quartier ?: ($e->ville ?: 'Autres')) : $e->ville);

        // Période affichée : de la plus ancienne date de début à la plus proche date de fin
        $debut = $toutes->min('garde_debut');
        $fin   = $toutes->min('garde_fin');

        $response = response()->view('site.pages.pharmacies-garde', compact('toutes', 'gardes', 'parQuartier', 'villes', 'ville', 'debut', 'fin'));

        // Retient la ville pour la prochaine visite (30 jours)
        if ($request->has('ville')) {
            $response->withCookie(cookie('ps_ville_garde', (string) $ville, 60 * 24 * 30));
        }

        return $response;
    }

    /* ====================== Administration ====================== */

    /** Module annuaire masqué : l'administration ne gère que les pharmacies */
    public static function pharmaciesSeules(): bool
    {
        return !config('sante.modules.annuaire');
    }

    /** Hôpitaux, cliniques, urgences… inaccessibles tant que l'annuaire est masqué */
    private function verifierType(Etablissement $etablissement): void
    {
        abort_if(self::pharmaciesSeules() && $etablissement->type !== 'pharmacie', 404);
    }

    public function index(Request $request)
    {
        $type = self::pharmaciesSeules() ? 'pharmacie'
            : (array_key_exists($request->type, Etablissement::TYPES) ? $request->type : null);

        $etablissements = Etablissement::when($type, fn($w) => $w->where('type', $type))
            ->when($request->filled('q'), fn($w) => $w->where('nom', 'LIKE', '%' . $request->q . '%'))
            ->orderByRaw("type = 'urgence' DESC")
            ->orderBy('type')->orderBy('ville')->orderBy('nom')
            ->paginate(40)
            ->withQueryString();

        $nbGarde = Etablissement::actifs()->deGarde()->count();
        $pharmacies = Etablissement::actifs()->where('type', 'pharmacie')->orderBy('ville')->orderBy('nom')->get(['id', 'nom', 'type', 'ville', 'quartier', 'garde_debut', 'garde_fin']);

        return view('admin.pages.etablissement.index', compact('etablissements', 'type', 'nbGarde', 'pharmacies'));
    }

    public function create()
    {
        $type = self::pharmaciesSeules() ? 'pharmacie' : request('type', 'pharmacie');

        return view('admin.pages.etablissement.form', ['etab' => new Etablissement(['actif' => true, 'type' => $type])]);
    }

    public function store(Request $request)
    {
        Etablissement::create($this->validated($request));

        Alert::toast(self::pharmaciesSeules() ? 'Pharmacie ajoutée' : 'Établissement ajouté', 'success');
        return $request->boolean('encore')
            ? redirect()->route('etablissement.create', ['type' => $request->type])
            : redirect()->route('etablissement.index');
    }

    public function edit(Etablissement $etablissement)
    {
        $this->verifierType($etablissement);

        return view('admin.pages.etablissement.form', ['etab' => $etablissement]);
    }

    public function update(Request $request, Etablissement $etablissement)
    {
        $this->verifierType($etablissement);
        $etablissement->update($this->validated($request));

        Alert::toast(self::pharmaciesSeules() ? 'Pharmacie modifiée' : 'Établissement modifié', 'success');
        return redirect()->route('etablissement.index');
    }

    public function toggle(Etablissement $etablissement)
    {
        $this->verifierType($etablissement);
        $etablissement->update(['actif' => !$etablissement->actif]);

        Alert::toast($etablissement->actif ? 'Affiché sur le site' : 'Masqué du site', 'info');
        return back();
    }

    public function destroy(Etablissement $etablissement)
    {
        $this->verifierType($etablissement);
        $etablissement->delete();

        Alert::toast(self::pharmaciesSeules() ? 'Pharmacie supprimée' : 'Établissement supprimé', 'success');
        return back();
    }

    /** Programme la garde de plusieurs pharmacies d'un coup (ex. la semaine) */
    public function garde(Request $request)
    {
        $data = $request->validate([
            'pharmacies'   => 'required|array|min:1',
            'pharmacies.*' => 'integer|exists:etablissements,id',
            'garde_debut'  => 'required|date',
            'garde_fin'    => 'required|date|after_or_equal:garde_debut',
            'remplacer'    => 'nullable|boolean',
        ], [
            'pharmacies.required'        => 'Cochez au moins une pharmacie.',
            'garde_fin.after_or_equal'   => 'La fin de garde doit être après le début.',
        ]);

        // Option : les autres pharmacies ne sont plus de garde sur cette période
        if ($request->boolean('remplacer')) {
            Etablissement::where('type', 'pharmacie')->whereNotIn('id', $data['pharmacies'])
                ->update(['garde_debut' => null, 'garde_fin' => null]);
        }

        Etablissement::whereIn('id', $data['pharmacies'])->where('type', 'pharmacie')
            ->update(['garde_debut' => $data['garde_debut'], 'garde_fin' => $data['garde_fin']]);

        // Mise à jour groupée (sans événement de modèle) : on vide le cache à la main
        Etablissement::viderCache();

        Alert::toast(count($data['pharmacies']) . ' pharmacie(s) de garde programmée(s)', 'success');
        return back();
    }

    /** Import des gardes depuis Google Drive : maintenant */
    public function importer()
    {
        $r = GardesImport::importer('manuel');

        Alert::toast($r['message'], $r['ok'] ? 'success' : 'error');
        return back();
    }

    /** Import d'un fichier envoyé depuis l'ordinateur (CSV, Excel, JSON) */
    public function importerFichier(Request $request)
    {
        $request->validate([
            'fichier' => 'required|file|max:5120|mimes:csv,txt,xlsx,json,html,htm',
        ], [
            'fichier.required' => 'Choisissez un fichier.',
            'fichier.mimes'    => 'Formats acceptés : CSV, Excel (.xlsx) ou JSON.',
            'fichier.max'      => 'Le fichier ne doit pas dépasser 5 Mo.',
        ]);

        $f = $request->file('fichier');
        $r = GardesImport::importerFichier((string) file_get_contents($f->getRealPath()), $f->getClientOriginalName());

        Alert::toast($r['message'], $r['ok'] ? 'success' : 'error');
        return back();
    }

    /** Import des gardes : lien Drive, identifiants Google, activation automatique */
    public function reglagesImport(Request $request)
    {
        $data = $request->validate([
            'gardes_csv_url'              => ['nullable', 'url', 'max:500', 'regex:#^https://#'],
            'gardes_source'               => 'nullable|string|max:100',
            'gardes_source_url'           => 'nullable|url|max:300',
            'gardes_google_client_id'     => ['nullable', 'string', 'max:200', 'regex:#\.apps\.googleusercontent\.com$#'],
            'gardes_google_client_secret' => 'nullable|string|max:200',
        ], [
            'gardes_csv_url.url'                => 'Collez le lien complet du dossier Google Drive (https://drive.google.com/…).',
            'gardes_csv_url.regex'              => 'Le lien doit commencer par https://',
            'gardes_google_client_id.regex'     => 'L\'ID client se termine par « .apps.googleusercontent.com ».',
        ]);

        // Deux formulaires envoient ici (dossier / identifiants Google) : on ne touche qu'aux champs reçus
        $valeurs = [];
        if ($request->has('gardes_csv_url')) {
            $url = trim((string) ($data['gardes_csv_url'] ?? ''));
            $valeurs += [
                'gardes_csv_url'     => $url,
                'gardes_source'      => $data['gardes_source'] ?? '',
                'gardes_source_url'  => $data['gardes_source_url'] ?? '',
                'gardes_import_auto' => $request->boolean('gardes_import_auto') && $url !== '',
            ];
            // Nouvelle source : le prochain import automatique la lira, même si un fichier a déjà été vu
            if ($url !== (string) GardesImport::url()) $valeurs['gardes_import_version'] = '';
        }

        if ($request->has('gardes_google_client_id')) {
            $id = trim((string) ($data['gardes_google_client_id'] ?? ''));
            if ($id !== (string) GoogleDrive::clientId() && GoogleDrive::connecte()) GoogleDrive::deconnecter();
            $valeurs['gardes_google_client_id'] = $id;
        }
        // Secret laissé vide = on garde celui enregistré (il n'est jamais réaffiché)
        if (!empty($data['gardes_google_client_secret'])) {
            $valeurs['gardes_google_client_secret'] = Crypt::encryptString(trim($data['gardes_google_client_secret']));
        }

        \App\Models\Setting::putMany($valeurs);
        \Illuminate\Support\Facades\Cache::forget('gardes_import_recent');

        Alert::toast('Réglages de l\'import enregistrés', 'success');
        return back();
    }

    /** Envoie l'administrateur sur Google pour autoriser la lecture de son Drive */
    public function driveConnecter(Request $request)
    {
        if (!GoogleDrive::configure()) {
            Alert::toast('Enregistrez d\'abord l\'ID client et le code secret Google.', 'error');
            return back();
        }

        $state = Str::random(40);
        $request->session()->put('drive_oauth_state', $state);

        return redirect()->away(GoogleDrive::urlConnexion($state));
    }

    /** Retour de Google après autorisation */
    public function driveCallback(Request $request)
    {
        $attendu = $request->session()->pull('drive_oauth_state');

        if ($request->filled('error')) {
            Alert::toast($request->error === 'access_denied' ? 'Connexion à Google Drive annulée.' : 'Google a refusé la connexion (' . Str::limit((string) $request->error, 60) . ').', 'error');
        } elseif (!$attendu || !hash_equals($attendu, (string) $request->state) || !$request->filled('code')) {
            Alert::toast('Lien de connexion expiré : recommencez.', 'error');
        } else {
            try {
                $compte = GoogleDrive::finaliser((string) $request->code);
                \App\Models\Setting::putMany(['gardes_import_version' => '']);
                Alert::toast('Google Drive connecté (' . $compte . ')', 'success');
            } catch (\Throwable $e) {
                Alert::toast(Str::limit($e->getMessage(), 200), 'error');
            }
        }

        return redirect()->route('etablissement.index');
    }

    public function driveDeconnecter()
    {
        GoogleDrive::deconnecter();

        Alert::toast('Google Drive déconnecté', 'info');
        return back();
    }

    public function finGarde(Etablissement $etablissement)
    {
        $etablissement->update(['garde_debut' => null, 'garde_fin' => null]);

        Alert::toast('Garde retirée', 'info');
        return back();
    }

    private function validated(Request $request): array
    {
        if (self::pharmaciesSeules()) $request->merge(['type' => 'pharmacie']);

        $data = $request->validate([
            'nom'         => 'required|string|max:150',
            'type'        => 'required|in:' . implode(',', array_keys(Etablissement::TYPES)),
            'ville'       => 'nullable|string|max:100',
            'quartier'    => 'nullable|string|max:100',
            'adresse'     => 'nullable|string|max:255',
            'telephone'   => 'nullable|string|max:40|required_if:type,urgence',
            'telephone_2' => 'nullable|string|max:40',
            'horaires'    => 'nullable|string|max:150',
            'maps_url'    => 'nullable|url|max:500',
            'latitude'    => 'nullable|numeric|between:-90,90|required_with:longitude',
            'longitude'   => 'nullable|numeric|between:-180,180|required_with:latitude',
            'notes'       => 'nullable|string|max:255',
            'garde_debut' => 'nullable|date|required_with:garde_fin',
            'garde_fin'   => 'nullable|date|after_or_equal:garde_debut|required_with:garde_debut',
        ], [
            'nom.required'            => 'Indiquez le nom.',
            'telephone.required_if'   => 'Un numéro d\'urgence doit avoir un numéro de téléphone.',
            'maps_url.url'            => 'Le lien Google Maps doit commencer par https://',
            'latitude.*'              => 'Latitude invalide (nombre entre -90 et 90, ex. 5.35438), à indiquer avec la longitude.',
            'longitude.*'             => 'Longitude invalide (nombre entre -180 et 180, ex. -4.03046), à indiquer avec la latitude.',
            'garde_fin.after_or_equal'=> 'La fin de garde doit être après le début.',
        ]);

        $data['ville'] = $data['ville'] ? mb_convert_case(trim($data['ville']), MB_CASE_TITLE) : null;
        $data['actif'] = $request->boolean('actif');
        if ($data['type'] !== 'pharmacie') $data['garde_debut'] = $data['garde_fin'] = null;

        return $data;
    }
}

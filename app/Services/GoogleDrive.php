<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Accès en lecture seule au Google Drive des pharmacies de garde, sans bibliothèque.
 *
 * Deux façons de lier le site :
 *  1. Compte de service (recommandé, le plus simple) : clé JSON téléchargée dans Google Cloud,
 *     chemin dans .env (GOOGLE_DRIVE_FICHIER_IDENTIFIANTS). Le dossier Drive est partagé
 *     (« Lecteur ») avec l'adresse …@….iam.gserviceaccount.com du compte de service.
 *     Ni écran de consentement, ni validation Google, ni expiration.
 *  2. OAuth (« Connecter Google Drive » dans l'administration), utilisé seulement sans compte
 *     de service. Réglages : gardes_google_client_id, gardes_google_client_secret (chiffré),
 *     gardes_drive_refresh (chiffré), gardes_drive_compte (e-mail du compte lié).
 */
class GoogleDrive
{
    public const SCOPE      = 'https://www.googleapis.com/auth/drive.readonly';
    private const AUTH_URL  = 'https://accounts.google.com/o/oauth2/v2/auth';
    private const TOKEN_URL = 'https://oauth2.googleapis.com/token';
    private const CACHE_JETON = 'gardes_drive_jeton';

    /* ---------- Réglages ---------- */

    public static function clientId(): ?string
    {
        return Setting::get('gardes_google_client_id');
    }

    public static function clientSecret(): ?string
    {
        return self::dechiffrer(Setting::get('gardes_google_client_secret'));
    }

    public static function configure(): bool
    {
        return self::clientId() && self::clientSecret();
    }

    public static function connecte(): bool
    {
        return self::modeService() || (bool) Setting::get('gardes_drive_refresh');
    }

    /** E-mail du compte Google lié (ou du compte de service) */
    public static function compte(): ?string
    {
        return self::modeService() ? self::compteService()['client_email'] : Setting::get('gardes_drive_compte');
    }

    /* ---------- Compte de service (clé JSON) ---------- */

    /** Chemin de la clé JSON indiqué dans .env (relatif à la racine du projet, ou absolu) */
    public static function fichierService(): ?string
    {
        $chemin = trim((string) config('sante.drive.identifiants'));
        if ($chemin === '') return null;

        return preg_match('#^([a-zA-Z]:)?[\\\\/]#', $chemin) ? $chemin : base_path($chemin);
    }

    public static function modeService(): bool
    {
        return self::compteService() !== null;
    }

    /** Contenu de la clé si le fichier est présent et valide, sinon null */
    public static function compteService(): ?array
    {
        static $cache = [];
        $fichier = self::fichierService();
        if (!$fichier) return null;
        if (array_key_exists($fichier, $cache)) return $cache[$fichier];

        $json = is_readable($fichier) ? json_decode((string) file_get_contents($fichier), true) : null;
        $ok = is_array($json) && ($json['type'] ?? '') === 'service_account'
            && !empty($json['client_email']) && !empty($json['private_key']);

        return $cache[$fichier] = $ok ? $json : null;
    }

    /** Problème de clé JSON à signaler dans l'administration, sinon null */
    public static function erreurService(): ?string
    {
        $fichier = self::fichierService();
        if (!$fichier || self::modeService()) return null;
        if (!is_file($fichier)) return 'Clé du compte de service introuvable (' . config('sante.drive.identifiants') . ') : déposez le fichier JSON à cet endroit sur le serveur.';

        return 'Le fichier ' . config('sante.drive.identifiants') . ' n\'est pas une clé de compte de service Google (fichier JSON de type « service_account »).';
    }

    /** Jeton d'accès du compte de service : JWT signé avec sa clé privée (RS256) */
    private static function jetonService(): string
    {
        $sa  = self::compteService();
        $b64 = fn(string $v) => rtrim(strtr(base64_encode($v), '+/', '-_'), '=');
        $now = time();
        $aud = $sa['token_uri'] ?? self::TOKEN_URL;

        $entree = $b64(json_encode(['alg' => 'RS256', 'typ' => 'JWT'])) . '.'
                . $b64(json_encode(['iss' => $sa['client_email'], 'scope' => self::SCOPE, 'aud' => $aud, 'iat' => $now - 30, 'exp' => $now + 3500]));

        $cle = openssl_pkey_get_private($sa['private_key']);
        if (!$cle || !openssl_sign($entree, $signature, $cle, OPENSSL_ALGO_SHA256)) {
            throw new ImportGardesException('La clé privée du compte de service est illisible : téléchargez une nouvelle clé JSON dans Google Cloud.');
        }

        $res = Http::asForm()->timeout(20)->post($aud, [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $entree . '.' . $b64($signature),
        ]);

        if (!$res->successful() || !$res->json('access_token')) {
            throw new ImportGardesException('Google refuse le compte de service (' . ($res->json('error_description') ?: $res->json('error') ?: 'code ' . $res->status())
                . '). Vérifiez que la clé n\'a pas été supprimée dans Google Cloud et que l\'heure du serveur est juste.');
        }

        Cache::put(self::CACHE_JETON . '_service', $res->json('access_token'), max(60, (int) $res->json('expires_in', 3600) - 120));

        return $res->json('access_token');
    }

    /** Adresse de retour à déclarer dans Google Cloud (« URI de redirection autorisés ») */
    public static function redirectUri(): string
    {
        $uri = route('etablissement.drive.callback');
        $hote = parse_url($uri, PHP_URL_HOST);

        // Google n'accepte le http que pour localhost ; en ligne l'adresse doit être identique, en https
        return in_array($hote, ['localhost', '127.0.0.1'], true) ? $uri : preg_replace('#^http://#', 'https://', $uri);
    }

    /* ---------- Connexion ---------- */

    public static function urlConnexion(string $state): string
    {
        return self::AUTH_URL . '?' . http_build_query([
            'client_id'     => self::clientId(),
            'redirect_uri'  => self::redirectUri(),
            'response_type' => 'code',
            'scope'         => self::SCOPE,
            'access_type'   => 'offline',   // jeton de renouvellement : le site lit Drive sans vous
            'prompt'        => 'consent',
            'state'         => $state,
        ]);
    }

    /** Échange le code reçu de Google contre les jetons ; retourne l'e-mail du compte */
    public static function finaliser(string $code): string
    {
        $res = Http::asForm()->timeout(20)->post(self::TOKEN_URL, [
            'code'          => $code,
            'client_id'     => self::clientId(),
            'client_secret' => self::clientSecret(),
            'redirect_uri'  => self::redirectUri(),
            'grant_type'    => 'authorization_code',
        ]);

        if (!$res->successful() || !$res->json('refresh_token')) {
            throw new ImportGardesException('Google a refusé la connexion (' . ($res->json('error_description') ?: $res->json('error') ?: 'code ' . $res->status()) . ').');
        }

        Cache::put(self::CACHE_JETON, $res->json('access_token'), max(60, (int) $res->json('expires_in', 3600) - 120));

        $compte = Http::withToken($res->json('access_token'))->timeout(15)
            ->get('https://www.googleapis.com/drive/v3/about', ['fields' => 'user(emailAddress)'])
            ->json('user.emailAddress') ?: 'compte Google';

        Setting::putMany([
            'gardes_drive_refresh' => Crypt::encryptString($res->json('refresh_token')),
            'gardes_drive_compte'  => $compte,
        ]);

        return $compte;
    }

    public static function deconnecter(): void
    {
        $refresh = self::dechiffrer(Setting::get('gardes_drive_refresh'));
        if ($refresh) {
            try { Http::asForm()->timeout(10)->post('https://oauth2.googleapis.com/revoke', ['token' => $refresh]); } catch (\Throwable) {}
        }

        Setting::putMany(['gardes_drive_refresh' => '', 'gardes_drive_compte' => '']);
        Cache::forget(self::CACHE_JETON);
    }

    /* ---------- Appels à l'API ---------- */

    /** GET sur l'API Drive avec le compte lié */
    public static function get(string $url, array $query = []): \Illuminate\Http\Client\Response
    {
        $res = Http::withToken(self::jeton())->timeout(25)->retry(2, 1000, null, false)->get($url, $query);

        // Jeton refusé (révoqué ou expiré plus tôt que prévu) : on en redemande un, une fois
        if ($res->status() === 401) {
            Cache::forget(self::CACHE_JETON);
            Cache::forget(self::CACHE_JETON . '_service');
            $res = Http::withToken(self::jeton())->timeout(25)->get($url, $query);
        }

        if ($res->successful()) return $res;

        $raison = $res->json('error.message') ?: ('code ' . $res->status());
        $aide = match (true) {
            $res->status() === 404 && self::modeService()                  => 'Dossier ou fichier introuvable : vérifiez le lien et que le dossier est bien partagé (Lecteur) avec ' . self::compte() . '.',
            $res->status() === 404                                         => 'Dossier ou fichier introuvable : vérifiez le lien et qu\'il appartient bien au compte ' . self::compte() . ' (ou lui est partagé).',
            $res->status() === 403 && str_contains($raison, 'not been used') => 'Activez « Google Drive API » dans le projet Google Cloud.',
            default                                                        => 'Réessayez plus tard ; si le problème continue, reconnectez Google Drive.',
        };

        throw new ImportGardesException('Google Drive refuse l\'accès (' . Str::limit($raison, 120) . '). ' . $aide);
    }

    /** Jeton d'accès valable 1 h, renouvelé automatiquement */
    private static function jeton(): string
    {
        if (self::modeService()) {
            return Cache::get(self::CACHE_JETON . '_service') ?: self::jetonService();
        }
        if ($e = self::erreurService()) throw new ImportGardesException($e);

        if (!self::connecte()) throw new ImportGardesException('Google Drive n\'est pas connecté : cliquez sur « Connecter Google Drive ».');

        if ($j = Cache::get(self::CACHE_JETON)) return $j;

        $res = Http::asForm()->timeout(20)->post(self::TOKEN_URL, [
            'client_id'     => self::clientId(),
            'client_secret' => self::clientSecret(),
            'refresh_token' => self::dechiffrer(Setting::get('gardes_drive_refresh')),
            'grant_type'    => 'refresh_token',
        ]);

        if (!$res->successful()) {
            if ($res->json('error') === 'invalid_grant') {
                // Accès retiré depuis le compte Google, ou expiré (application Google Cloud restée « en test »)
                Setting::putMany(['gardes_drive_refresh' => '']);
                throw new ImportGardesException('L\'accès à Google Drive a expiré ou a été retiré : cliquez sur « Connecter Google Drive » pour le rétablir.');
            }
            throw new ImportGardesException('Impossible de renouveler l\'accès à Google Drive (' . ($res->json('error_description') ?: $res->json('error') ?: 'code ' . $res->status()) . ').');
        }

        Cache::put(self::CACHE_JETON, $res->json('access_token'), max(60, (int) $res->json('expires_in', 3600) - 120));

        return $res->json('access_token');
    }

    private static function dechiffrer(?string $v): ?string
    {
        if (!$v) return null;
        try { return Crypt::decryptString($v); } catch (\Throwable) { return null; }
    }
}

<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PushAbonne;
use App\Models\PushMessage;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Notifications Web Push sans bibliothèque externe.
 *
 * On envoie une notification « vide » (sans contenu chiffré) à chaque appareil abonné :
 * le service worker (public/sw.js) se réveille, lit la dernière notification sur
 * /push/derniere puis l'affiche. Seule la signature VAPID est nécessaire (OpenSSL).
 * Les clés VAPID sont créées automatiquement au premier besoin et gardées dans les paramètres.
 */
class WebPush
{
    private const LOT = 50;          // envois simultanés
    private const ECHECS_MAX = 5;    // au-delà, l'abonnement est supprimé

    /** Clé publique (format attendu par le navigateur), créée si besoin */
    public static function clePublique(): ?string
    {
        return self::cles()['public'] ?? null;
    }

    public static function actif(): bool
    {
        return self::clePublique() !== null;
    }

    /** Notification automatique à la publication d'un article (une seule fois par article) */
    public static function notifierArticle(Post $post): void
    {
        try {
            if (!config('sante.modules.notifications')) return;
            if ($post->published !== 'public' || !$post->slug) return;
            if (!(int) Setting::get('push_auto', 1)) return;
            if (strtolower($post->category->title ?? '') === 'sondage') return;
            if (PushMessage::where('post_id', $post->id)->exists()) return;
            if (!PushAbonne::exists() || !self::actif()) return;

            $message = PushMessage::create([
                'titre'   => Str::limit($post->title ?: 'Nouvel article', 110, '…'),
                'corps'   => Str::limit($post->meta_description ?: trim(html_entity_decode(strip_tags((string) $post->description))), 150, '…'),
                'url'     => route('post.detail', ['slug' => $post->slug], false),
                'post_id' => $post->id,
            ]);

            dispatch(fn() => self::envoyer($message))->afterResponse();
        } catch (\Throwable $e) {
            Log::warning('Web Push : notification article impossible', ['post' => $post->id, 'erreur' => $e->getMessage()]);
        }
    }

    /** Envoie la notification à tous les abonnés. Retourne [envoyés, échecs]. */
    public static function envoyer(PushMessage $message): array
    {
        $cles = self::cles();
        if (!$cles) return [0, 0];

        @set_time_limit(0);
        $ok = $ko = 0;
        $jetons = [];

        PushAbonne::query()->orderBy('id')->chunkById(self::LOT, function ($abonnes) use (&$ok, &$ko, &$jetons, $cles) {
            $reponses = Http::pool(function ($pool) use ($abonnes, &$jetons, $cles) {
                foreach ($abonnes as $a) {
                    $origine = self::origine($a->endpoint);
                    $jetons[$origine] ??= self::jwt($origine, $cles['private']);

                    $pool->as((string) $a->id)
                        ->timeout(10)
                        ->withHeaders([
                            'TTL'           => '86400',
                            'Urgency'       => 'normal',
                            'Authorization' => 'vapid t=' . $jetons[$origine] . ', k=' . $cles['public'],
                        ])
                        ->withBody('', 'application/octet-stream')
                        ->post($a->endpoint);
                }
            });

            foreach ($abonnes as $a) {
                $r = $reponses[(string) $a->id] ?? null;
                $status = ($r instanceof \Illuminate\Http\Client\Response) ? $r->status() : 0;

                if ($status >= 200 && $status < 300) {
                    $ok++;
                    if ($a->echecs) $a->update(['echecs' => 0]);
                } elseif (in_array($status, [404, 410], true) || $a->echecs + 1 >= self::ECHECS_MAX) {
                    $ko++;
                    $a->delete();   // appareil désabonné ou introuvable
                } else {
                    $ko++;
                    $a->increment('echecs');
                }
            }
        });

        $message->forceFill(['envoyes' => $ok, 'echecs' => $ko])->save();

        return [$ok, $ko];
    }

    /* ---------------------------------------------------------------- */

    /** ['public' => base64url, 'private' => PEM] ; créées au premier appel */
    private static function cles(): ?array
    {
        $pub  = Setting::get('vapid_public');
        $priv = Setting::get('vapid_private');
        if ($pub && $priv) return ['public' => $pub, 'private' => $priv];

        try {
            $options = ['curve_name' => 'prime256v1', 'private_key_type' => OPENSSL_KEYTYPE_EC];
            $key = @openssl_pkey_new($options);

            // Windows : OpenSSL a parfois besoin du chemin de sa configuration
            if (!$key) {
                foreach (array_filter([getenv('OPENSSL_CONF'), PHP_BINARY ? dirname(PHP_BINARY) . '/extras/ssl/openssl.cnf' : null]) as $cnf) {
                    if (is_file($cnf) && ($key = @openssl_pkey_new($options + ['config' => $cnf]))) break;
                }
            }
            if (!$key) return null;

            $details = openssl_pkey_get_details($key);
            $public  = self::b64("\x04" . str_pad($details['ec']['x'], 32, "\0", STR_PAD_LEFT) . str_pad($details['ec']['y'], 32, "\0", STR_PAD_LEFT));
            openssl_pkey_export($key, $pem, null, isset($cnf) ? ['config' => $cnf] : []);

            if (!$pem) return null;
            Setting::putMany(['vapid_public' => $public, 'vapid_private' => $pem]);

            return ['public' => $public, 'private' => $pem];
        } catch (\Throwable $e) {
            Log::warning('Web Push : création des clés impossible', ['erreur' => $e->getMessage()]);
            return null;
        }
    }

    private static function origine(string $endpoint): string
    {
        $p = parse_url($endpoint);

        return ($p['scheme'] ?? 'https') . '://' . ($p['host'] ?? '') . (isset($p['port']) ? ':' . $p['port'] : '');
    }

    /** Jeton VAPID (JWT signé ES256), valable 12 h */
    private static function jwt(string $audience, string $pem): string
    {
        $contact = Setting::get('email_1') ? 'mailto:' . Setting::get('email_1') : url('/');
        $data = self::b64(json_encode(['typ' => 'JWT', 'alg' => 'ES256']))
            . '.' . self::b64(json_encode(['aud' => $audience, 'exp' => time() + 12 * 3600, 'sub' => $contact], JSON_UNESCAPED_SLASHES));

        openssl_sign($data, $der, openssl_pkey_get_private($pem), OPENSSL_ALGO_SHA256);

        return $data . '.' . self::b64(self::derVersBrut($der));
    }

    /** Signature ECDSA : DER (r, s) → 64 octets bruts r||s attendus par JWT */
    private static function derVersBrut(string $der): string
    {
        $pos = 2;                                   // SEQUENCE + longueur
        $out = '';
        for ($i = 0; $i < 2; $i++) {
            $len = ord($der[$pos + 1]);             // INTEGER + longueur
            $int = ltrim(substr($der, $pos + 2, $len), "\0");
            $out .= str_pad($int, 32, "\0", STR_PAD_LEFT);
            $pos += 2 + $len;
        }

        return $out;
    }

    private static function b64(string $s): string
    {
        return rtrim(strtr(base64_encode($s), '+/', '-_'), '=');
    }
}

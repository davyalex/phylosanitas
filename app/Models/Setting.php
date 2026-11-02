<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

/**
 * Paramètres du site (Admin > Configuration), stockés en clé / valeur.
 * Lecture : Setting::get('slogan') ou la variable $site dans les vues.
 */
class Setting extends Model
{
    public const CACHE_KEY = 'site_settings';

    protected $primaryKey = 'key';
    public $incrementing  = false;
    protected $keyType    = 'string';
    protected $fillable   = ['key', 'value'];

    /** Tous les paramètres (cache permanent, vidé à chaque enregistrement) */
    public static function allCached(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            try {
                return self::pluck('value', 'key')->all();
            } catch (\Throwable) {
                return [];
            }
        });
    }

    public static function get(string $key, $default = null)
    {
        $value = self::allCached()[$key] ?? null;

        return ($value === null || $value === '') ? $default : $value;
    }

    /** Enregistre plusieurs paramètres d'un coup */
    public static function putMany(array $values): void
    {
        foreach ($values as $key => $value) {
            self::updateOrCreate(['key' => $key], ['value' => is_bool($value) ? (int) $value : $value]);
        }
        Cache::forget(self::CACHE_KEY);
    }

    /** Données prêtes à l'emploi pour les vues */
    public static function forViews(): array
    {
        $s = self::allCached();
        $g = fn($k, $d = null) => (isset($s[$k]) && $s[$k] !== '') ? $s[$k] : $d;

        $phones = [];
        $emails = [];
        foreach ([1, 2, 3] as $i) {
            if ($tel = $g("telephone_$i")) {
                $phones[] = ['label' => $g("telephone_{$i}_label"), 'value' => $tel, 'href' => 'tel:' . preg_replace('/[^\d+]/', '', $tel)];
            }
            if ($mail = $g("email_$i")) {
                $emails[] = ['label' => $g("email_{$i}_label"), 'value' => $mail, 'href' => 'mailto:' . $mail];
            }
        }

        $socials = [];
        foreach (self::SOCIALS as $key => $meta) {
            if ($url = $g($key)) $socials[$key] = ['url' => $url] + $meta;
        }

        $whatsapp = $g('whatsapp');

        return [
            'nom'           => $g('site_nom', config('app.name')),
            'slogan'        => $g('slogan', 'Votre santé, notre priorité'),
            'description'   => $g('description', ''),
            'logo'          => $g('logo') ? Storage::disk('public')->url($g('logo')) : asset('assets_site/img/logo/logo_tp.png'),
            'adresse'       => $g('adresse'),
            'ville'         => $g('ville'),
            'pays_code'     => $g('pays_code', 'CI'),
            'horaires'      => $g('horaires'),
            'phones'        => $phones,
            'emails'        => $emails,
            'whatsapp'      => $whatsapp,
            'whatsapp_url'  => $whatsapp ? 'https://wa.me/' . preg_replace('/\D/', '', $whatsapp) : null,
            'socials'       => $socials,
            'show_visits'   => (bool) $g('afficher_visites', 1),
            'visites_depart'=> (int) $g('visites_depart', 0),
            'avertissement' => $g('avertissement'),
        ];
    }

    public const SOCIALS = [
        'facebook'  => ['label' => 'Facebook',  'icon' => 'facebook'],
        'twitter'   => ['label' => 'X (Twitter)', 'icon' => 'twitter-x'],
        'instagram' => ['label' => 'Instagram', 'icon' => 'instagram'],
        'linkedin'  => ['label' => 'LinkedIn',  'icon' => 'linkedin'],
        'youtube'   => ['label' => 'YouTube',   'icon' => 'youtube'],
        'tiktok'    => ['label' => 'TikTok',    'icon' => 'tiktok'],
    ];
}

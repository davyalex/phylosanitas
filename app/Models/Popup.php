<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Popup d'information (affiche de campagne, annonce…).
 * Un seul popup s'affiche à la fois sur le site : le plus récent en diffusion.
 */
class Popup extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    public const CACHE_KEY = 'site_popup';

    public const FREQUENCES = [
        'once'    => ['label' => 'Une seule fois',     'hint' => 'Le visiteur ne le revoit plus après l\'avoir fermé'],
        'daily'   => ['label' => 'Une fois par jour',  'hint' => 'Réapparaît le lendemain'],
        'session' => ['label' => 'À chaque visite',    'hint' => 'Réapparaît quand le visiteur revient sur le site'],
    ];

    public const PAGES = [
        'tout'    => ['label' => 'Tout le site',          'hint' => 'Sur la première page consultée'],
        'accueil' => ['label' => 'Page d\'accueil seule', 'hint' => 'Uniquement sur l\'accueil'],
    ];

    protected $fillable = [
        'titre', 'texte', 'bouton_texte', 'lien', 'pages', 'frequence', 'delai',
        'actif', 'debut_at', 'fin_at',
    ];

    protected $casts = [
        'actif'    => 'boolean',
        'debut_at' => 'datetime',
        'fin_at'   => 'datetime',
        'delai'    => 'integer',
        'vues'     => 'integer',
        'clics'    => 'integer',
    ];

    protected static function booted(): void
    {
        $flush = function () { Cache::forget(self::CACHE_KEY); Cache::forget('admin_badges'); };
        static::saved($flush);
        static::deleted($flush);
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('web')
            ->fit(Manipulations::FIT_MAX, 1200, 1500)
            ->format(Manipulations::FORMAT_WEBP)
            ->quality(84)
            ->performOnCollections('image')
            ->nonQueued();
    }

    public function scopeDiffusable(Builder $q): Builder
    {
        $now = now();

        return $q->where('actif', true)
            ->where(fn($q) => $q->whereNull('debut_at')->orWhere('debut_at', '<=', $now))
            ->where(fn($q) => $q->whereNull('fin_at')->orWhere('fin_at', '>=', $now));
    }

    public function getStatutAttribute(): string
    {
        if (!$this->actif) return 'off';
        if ($this->debut_at && $this->debut_at->isFuture()) return 'scheduled';
        if ($this->fin_at && $this->fin_at->isPast()) return 'expired';
        return 'live';
    }

    public function getCtrAttribute(): float
    {
        return $this->vues > 0 ? round($this->clics / $this->vues * 100, 1) : 0.0;
    }

    public function imageUrl(): ?string
    {
        $media = $this->getFirstMedia('image');
        if (!$media) return null;

        return $media->hasGeneratedConversion('web') ? $media->getUrl('web') : $media->getUrl();
    }

    /** Le popup à afficher sur le site (cache 5 min), ou null */
    public static function forSite(): ?object
    {
        $popup = Cache::remember(self::CACHE_KEY, 300, function () {
            try {
                $p = self::diffusable()
                    ->with(['media' => fn($q) => $q->where('collection_name', 'image')])
                    ->latest('debut_at')->latest()
                    ->first();

                if (!$p) return false;

                return (object) [
                    'id'           => $p->id,
                    'titre'        => $p->titre,
                    'texte'        => $p->texte,
                    'bouton_texte' => $p->bouton_texte,
                    'lien'         => $p->lien,
                    'pages'        => $p->pages,
                    'frequence'    => $p->frequence,
                    'delai'        => $p->delai,
                    'image'        => $p->imageUrl(),
                    'fin_at'       => $p->fin_at,
                ];
            } catch (\Throwable) {
                return false;
            }
        });

        // Le cache peut survivre quelques minutes à la date de fin
        if (!$popup || ($popup->fin_at && $popup->fin_at->isPast())) return null;

        return $popup;
    }
}

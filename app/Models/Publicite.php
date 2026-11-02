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

class Publicite extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    public const CACHE_KEY = 'site_publicites';

    /** Emplacements disponibles sur le site, avec le format conseillé */
    public const EMPLACEMENTS = [
        'leaderboard' => ['label' => 'Bandeau horizontal', 'taille' => '728 × 90 px (mobile : 320 × 100)', 'pages' => 'Accueil, sous « À la une »'],
        'rectangle'   => ['label' => 'Rectangle barre latérale', 'taille' => '300 × 250 px', 'pages' => 'Toutes les pages, barre latérale'],
        'infeed'      => ['label' => 'Dans les listes', 'taille' => '728 × 90 px ou format large', 'pages' => 'Listes d\'articles par catégorie'],
        'inarticle'   => ['label' => 'Dans un article', 'taille' => '728 × 90 px ou format large', 'pages' => 'Page article, après le 3e paragraphe'],
    ];

    protected $fillable = [
        'titre', 'annonceur', 'emplacement', 'type', 'lien', 'code',
        'actif', 'debut_at', 'fin_at', 'priorite',
    ];

    protected $casts = [
        'actif'       => 'boolean',
        'debut_at'    => 'datetime',
        'fin_at'      => 'datetime',
        'priorite'    => 'integer',
        'impressions' => 'integer',
        'clics'       => 'integer',
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
            ->fit(Manipulations::FIT_MAX, 1456, 1456)
            ->format(Manipulations::FORMAT_WEBP)
            ->quality(82)
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

    /** Taux de clic en % */
    public function getCtrAttribute(): float
    {
        return $this->impressions > 0 ? round($this->clics / $this->impressions * 100, 2) : 0.0;
    }

    public function imageUrl(): ?string
    {
        $media = $this->getFirstMedia('image');
        if (!$media) return null;

        return $media->hasGeneratedConversion('web') ? $media->getUrl('web') : $media->getUrl();
    }

    /**
     * Publicités diffusables groupées par emplacement (cache 5 min).
     * Seules les colonnes utiles au rendu sont gardées en cache.
     */
    public static function forSite()
    {
        return Cache::remember(self::CACHE_KEY, 300, function () {
            try {
                return self::diffusable()
                    ->with(['media' => fn($q) => $q->where('collection_name', 'image')])
                    ->get()
                    ->map(fn($p) => (object) [
                        'id'          => $p->id,
                        'titre'       => $p->titre,
                        'emplacement' => $p->emplacement,
                        'type'        => $p->type,
                        'lien'        => $p->lien,
                        'code'        => $p->code,
                        'priorite'    => max(1, $p->priorite),
                        'image'       => $p->imageUrl(),
                        'fin_at'      => $p->fin_at,
                    ])
                    ->groupBy('emplacement');
            } catch (\Throwable) {
                return collect();
            }
        });
    }

    /** Tire une publicité pour un emplacement (pondérée par la priorité) */
    public static function pick(string $emplacement): ?object
    {
        $ads = (self::forSite()[$emplacement] ?? collect())
            ->filter(fn($a) => !$a->fin_at || $a->fin_at->isFuture())
            ->filter(fn($a) => $a->type === 'code' ? filled($a->code) : filled($a->image));

        if ($ads->isEmpty()) return null;

        $total = $ads->sum('priorite');
        $roll  = random_int(1, $total);
        foreach ($ads as $ad) {
            $roll -= $ad->priorite;
            if ($roll <= 0) return $ad;
        }

        return $ads->first();
    }
}

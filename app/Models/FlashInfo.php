<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class FlashInfo extends Model
{
    use SoftDeletes;

    public const CACHE_KEY = 'site_flash_infos';

    public const NIVEAUX = [
        'info'      => 'Information',
        'important' => 'Important',
        'urgent'    => 'Urgent',
    ];

    protected $fillable = ['message', 'lien', 'niveau', 'actif', 'debut_at', 'fin_at', 'ordre', 'user_id'];

    protected $casts = [
        'actif'    => 'boolean',
        'debut_at' => 'datetime',
        'fin_at'   => 'datetime',
        'ordre'    => 'integer',
    ];

    protected static function booted(): void
    {
        // Le bandeau public est mis en cache : on l'invalide à chaque modification
        $flush = function () { Cache::forget(self::CACHE_KEY); Cache::forget('admin_badges'); };
        static::saved($flush);
        static::deleted($flush);
    }

    /** Infos actives et dans leur période de diffusion */
    public function scopeVisible(Builder $q): Builder
    {
        $now = now();

        return $q->where('actif', true)
            ->where(fn($q) => $q->whereNull('debut_at')->orWhere('debut_at', '<=', $now))
            ->where(fn($q) => $q->whereNull('fin_at')->orWhere('fin_at', '>=', $now))
            ->orderByRaw("FIELD(niveau, 'urgent', 'important', 'info')")
            ->orderBy('ordre')
            ->latest();
    }

    /** Statut lisible pour l'admin : en ligne, programmée, expirée, désactivée */
    public function getStatutAttribute(): string
    {
        if (!$this->actif) return 'off';
        if ($this->debut_at && $this->debut_at->isFuture()) return 'scheduled';
        if ($this->fin_at && $this->fin_at->isPast()) return 'expired';
        return 'live';
    }

    /** Infos visibles pour le site (cache 5 min, vidé à chaque modification) */
    public static function forSite()
    {
        return Cache::remember(self::CACHE_KEY, 300, function () {
            try {
                return self::visible()->take(8)->get(['id', 'message', 'lien', 'niveau']);
            } catch (\Throwable) {
                return collect();
            }
        });
    }
}

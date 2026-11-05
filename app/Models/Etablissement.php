<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Pharmacie (et sa période de garde). La colonne « type » est gardée pour accueillir plus tard
 * les autres établissements de l'annuaire santé (hôpitaux, cliniques…) ; pour l'instant : « pharmacie ».
 */
class Etablissement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nom', 'type', 'ville', 'quartier', 'adresse', 'telephone', 'telephone_2',
        'horaires', 'maps_url', 'latitude', 'longitude', 'notes', 'garde_debut', 'garde_fin', 'actif',
    ];

    protected $casts = [
        'actif'       => 'boolean',
        'latitude'    => 'float',
        'longitude'   => 'float',
        'garde_debut' => 'date',
        'garde_fin'   => 'date',
    ];

    public const CACHE_GARDES = 'pharmacies_de_garde';

    protected static function booted(): void
    {
        static::saved(fn() => self::viderCache());
        static::deleted(fn() => self::viderCache());
    }

    public static function viderCache(): void
    {
        \Illuminate\Support\Facades\Cache::forget(self::CACHE_GARDES);
        \Illuminate\Support\Facades\Cache::forget('admin_badges');
    }

    /**
     * Pharmacies de garde aujourd'hui (cache 10 min, recalculé chaque jour).
     * Utilisé par l'en-tête, l'accueil, la barre latérale et la page dédiée.
     */
    public static function gardesDuJour(): \Illuminate\Support\Collection
    {
        $data = \Illuminate\Support\Facades\Cache::remember(self::CACHE_GARDES, 600, function () {
            try {
                return [
                    'jour'  => now()->toDateString(),
                    'liste' => self::actifs()->deGarde()
                        ->orderByRaw("ville = 'Abidjan' DESC")->orderBy('ville')->orderBy('quartier')->orderBy('nom')
                        ->get(),
                ];
            } catch (\Throwable) {
                return ['jour' => now()->toDateString(), 'liste' => collect()];
            }
        });

        // Calculé la veille : on recharge
        if (($data['jour'] ?? null) !== now()->toDateString()) {
            self::viderCache();
            return self::gardesDuJour();
        }

        return $data['liste'];
    }

    public function scopeActifs($q)
    {
        return $q->where('actif', true);
    }

    public function scopeDeGarde($q)
    {
        $today = now()->toDateString();

        return $q->where('type', 'pharmacie')
            ->whereDate('garde_debut', '<=', $today)
            ->whereDate('garde_fin', '>=', $today);
    }

    public function estDeGarde(): bool
    {
        return $this->type === 'pharmacie' && $this->garde_debut && $this->garde_fin
            && now()->between($this->garde_debut->copy()->startOfDay(), $this->garde_fin->copy()->endOfDay());
    }

    public static function telHref(?string $tel): ?string
    {
        return $tel ? 'tel:' . preg_replace('/[^\d+]/', '', $tel) : null;
    }

    /** Lien carte : celui saisi, sinon l'itinéraire vers les coordonnées GPS, sinon une recherche sur le nom et l'adresse */
    public function carteUrl(): ?string
    {
        if ($this->maps_url) return $this->maps_url;
        if ($this->latitude !== null && $this->longitude !== null) {
            return 'https://www.google.com/maps/dir/?api=1&destination=' . $this->latitude . ',' . $this->longitude;
        }
        if (!$this->adresse && !$this->quartier) return null;

        $q = implode(', ', array_filter([$this->nom, $this->adresse, $this->quartier, $this->ville]));

        return 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($q);
    }
}

<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Journée ou mois de santé, qui revient chaque année.
 * jour vide = campagne sur tout le mois (Octobre rose…).
 */
class JourneeSante extends Model
{
    protected $table = 'journees_sante';

    public const CACHE_KEY = 'journee_sante_bandeau';

    public const MOIS = [1 => 'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];

    protected $fillable = ['titre', 'jour', 'mois', 'description', 'couleur', 'lien', 'annonce_jours', 'actif'];

    protected $casts = [
        'actif'         => 'boolean',
        'jour'          => 'integer',
        'mois'          => 'integer',
        'annonce_jours' => 'integer',
    ];

    protected static function booted(): void
    {
        $flush = fn() => Cache::forget(self::CACHE_KEY);
        static::saved($flush);
        static::deleted($flush);
    }

    public function estMensuelle(): bool
    {
        return !$this->jour;
    }

    /** Prochaine occurrence (aujourd'hui compris) : [début, fin] */
    public function prochaineOccurrence(?Carbon $ref = null): array
    {
        $ref = ($ref ?? now())->copy()->startOfDay();
        $debut = $fin = null;

        foreach ([$ref->year, $ref->year + 1] as $annee) {
            if ($this->estMensuelle()) {
                $debut = Carbon::create($annee, $this->mois, 1)->startOfDay();
                $fin   = $debut->copy()->endOfMonth();
            } else {
                $jour  = min($this->jour, Carbon::create($annee, $this->mois, 1)->daysInMonth);
                $debut = Carbon::create($annee, $this->mois, $jour)->startOfDay();
                $fin   = $debut->copy()->endOfDay();
            }
            if ($fin->gte($ref)) break;
        }

        return [$debut, $fin];
    }

    public function getDateLibelleAttribute(): string
    {
        return $this->estMensuelle()
            ? 'Tout le mois de ' . self::MOIS[$this->mois]
            : ($this->jour === 1 ? '1er' : $this->jour) . ' ' . self::MOIS[$this->mois];
    }

    /**
     * Bandeau du site : la journée du jour, sinon la campagne du mois,
     * sinon la prochaine journée annoncée (dans sa période d'annonce). Cache 1 h.
     */
    public static function pourBandeau(): ?object
    {
        $data = Cache::remember(self::CACHE_KEY, 3600, function () {
            try {
                $today = now()->startOfDay();
                $candidats = [];

                foreach (self::where('actif', true)->get() as $j) {
                    [$debut, $fin] = $j->prochaineOccurrence($today);
                    $enCours = $debut->lte($today) && $fin->gte($today);
                    $dans    = $enCours ? 0 : (int) $today->diffInDays($debut);

                    if (!$enCours && $dans > $j->annonce_jours) continue;

                    // Priorité : journée du jour > campagne du mois > annonce la plus proche
                    $rang = $enCours ? ($j->estMensuelle() ? 1 : 0) : 2 + $dans;

                    $candidats[] = [
                        'rang'    => $rang,
                        'id'      => $j->id,
                        'titre'   => $j->titre,
                        'texte'   => $j->description,
                        'couleur' => $j->couleur,
                        'lien'    => $j->lien,
                        'etat'    => $enCours ? ($j->estMensuelle() ? 'mois' : 'jour') : 'bientot',
                        'dans'    => $dans,
                        'date'    => $debut->toDateString(),
                        'expire'  => now()->endOfDay()->toIso8601String(),
                    ];
                }

                usort($candidats, fn($a, $b) => $a['rang'] <=> $b['rang']);

                return $candidats[0] ?? false;
            } catch (\Throwable) {
                return false;
            }
        });

        // Le cache peut avoir été calculé la veille
        if (!$data || Carbon::parse($data['expire'])->isPast()) {
            if ($data) Cache::forget(self::CACHE_KEY);
            return null;
        }

        return (object) $data;
    }

    /**
     * Calendrier de l'année en cours, classé en trois groupes :
     *  - 'en_cours' : journée du jour, campagne du mois en cours ;
     *  - 'a_venir'  : reste de l'année, du plus proche au plus lointain ;
     *  - 'passees'  : déjà passées cette année (avec la date de retour l'an prochain).
     */
    public static function calendrierAnnee(?Carbon $ref = null): array
    {
        $today = ($ref ?? now())->copy()->startOfDay();
        $annee = $today->year;
        $groupes = ['en_cours' => collect(), 'a_venir' => collect(), 'passees' => collect()];

        foreach (self::where('actif', true)->get() as $j) {
            if ($j->estMensuelle()) {
                $debut = Carbon::create($annee, $j->mois, 1)->startOfDay();
                $fin   = $debut->copy()->endOfMonth();
            } else {
                $jour  = min($j->jour, Carbon::create($annee, $j->mois, 1)->daysInMonth);
                $debut = Carbon::create($annee, $j->mois, $jour)->startOfDay();
                $fin   = $debut->copy()->endOfDay();
            }

            $j->debut = $debut;
            $j->fin   = $fin;

            if ($fin->lt($today)) {
                $j->etat = 'passee';
                $j->retour = $j->prochaineOccurrence($today)[0];   // même date, l'an prochain
                $groupes['passees']->push($j);
            } elseif ($debut->gt($today)) {
                $j->etat = 'a_venir';
                $j->dans = (int) $today->diffInDays($debut);
                $groupes['a_venir']->push($j);
            } else {
                $j->etat = 'en_cours';
                $groupes['en_cours']->push($j);
            }
        }

        // Journée du jour avant la campagne du mois ; à venir et passées dans l'ordre chronologique
        $groupes['en_cours'] = $groupes['en_cours']->sortBy(fn($j) => $j->estMensuelle() ? 1 : 0)->values();
        $groupes['a_venir']  = $groupes['a_venir']->sortBy(fn($j) => $j->debut->timestamp . '-' . ($j->estMensuelle() ? 0 : 1))->values();
        $groupes['passees']  = $groupes['passees']->sortBy(fn($j) => $j->debut->timestamp)->values();

        return $groupes;
    }

    /** Toutes les journées actives, triées à partir d'aujourd'hui */
    public static function aVenir(): \Illuminate\Support\Collection
    {
        return self::where('actif', true)->get()
            ->map(function ($j) {
                [$j->debut, $j->fin] = $j->prochaineOccurrence();
                $j->en_cours = $j->debut->lte(now()) && $j->fin->gte(now()->startOfDay());
                return $j;
            })
            ->sortBy(fn($j) => $j->debut->timestamp)
            ->values();
    }
}

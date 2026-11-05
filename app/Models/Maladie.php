<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/** Fiche de l'abécédaire des maladies */
class Maladie extends Model
{
    use SoftDeletes, Sluggable;

    /** Rubriques de la fiche : champ => [titre, icône] */
    public const RUBRIQUES = [
        'symptomes'       => ['Symptômes', 'thermometer-half'],
        'causes'          => ['Causes et facteurs de risque', 'diagram-3'],
        'prevention'      => ['Prévention', 'shield-check'],
        'traitement'      => ['Traitement', 'capsule'],
        'quand_consulter' => ['Quand consulter ?', 'hospital'],
    ];

    protected $fillable = [
        'nom', 'slug', 'autres_noms', 'resume', 'symptomes', 'causes', 'prevention',
        'traitement', 'quand_consulter', 'mots_cles', 'publie', 'reviewer_id',
    ];

    protected $casts = ['publie' => 'boolean'];

    protected static function booted(): void
    {
        // Liste « Maladies fréquentes » de l'accueil
        $flush = fn() => \Illuminate\Support\Facades\Cache::forget('home_maladies');
        static::saved($flush);
        static::deleted($flush);
    }

    public function sluggable(): array
    {
        return ['slug' => ['source' => 'nom']];
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function scopePubliees($q)
    {
        return $q->where('publie', true);
    }

    /** Première lettre, sans accent (É → E) */
    public function getLettreAttribute(): string
    {
        $l = strtoupper(Str::substr(Str::ascii($this->nom), 0, 1));

        return ctype_alpha($l) ? $l : '#';
    }

    /** Texte saisi « une ligne = un élément » → liste */
    public static function lignes(?string $texte): array
    {
        return array_values(array_filter(array_map(
            fn($l) => trim(ltrim(trim($l), "-•*–")),
            preg_split('/\R/u', (string) $texte)
        ), fn($l) => $l !== ''));
    }

    /** Mots servant à retrouver les articles liés */
    public function motsCles(): array
    {
        $mots = $this->mots_cles ? explode(',', $this->mots_cles) : [$this->nom];

        return array_values(array_filter(array_map('trim', $mots), fn($m) => mb_strlen($m) >= 3));
    }
}

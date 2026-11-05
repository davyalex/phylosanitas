<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/** Quiz « Vrai ou faux ? » */
class Quiz extends Model
{
    use SoftDeletes, Sluggable;

    public const CACHE_ACCUEIL = 'home_quiz';

    protected $fillable = ['titre', 'slug', 'intro', 'actif'];

    protected static function booted(): void
    {
        $flush = fn() => \Illuminate\Support\Facades\Cache::forget(self::CACHE_ACCUEIL);
        static::saved($flush);
        static::deleted($flush);
    }

    protected $casts = [
        'actif'          => 'boolean',
        'participations' => 'integer',
        'score_total'    => 'integer',
    ];

    public function sluggable(): array
    {
        return ['slug' => ['source' => 'titre']];
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('ordre')->orderBy('id');
    }

    public function scopeEnLigne($q)
    {
        return $q->where('actif', true)->has('questions');
    }

    /** Score moyen des participants, en % */
    public function getMoyenneAttribute(): ?int
    {
        $nb = $this->questions_count ?? $this->questions()->count();
        if (!$this->participations || !$nb) return null;

        return (int) round($this->score_total / ($this->participations * $nb) * 100);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/** Question d'un lecteur pour « Le médecin répond » */
class QuestionLecteur extends Model
{
    use SoftDeletes;

    protected $table = 'questions_lecteurs';

    public const TRANCHES_AGE = ['moins de 18 ans', '18-30 ans', '31-45 ans', '46-60 ans', 'plus de 60 ans'];

    protected $fillable = [
        'nom', 'email', 'tranche_age', 'question', 'titre', 'slug', 'reponse',
        'statut', 'category_id', 'repondu_par', 'repondu_at',
    ];

    protected $casts = [
        'repondu_at' => 'datetime',
        'vues'       => 'integer',
    ];

    protected static function booted(): void
    {
        $flush = fn() => Cache::forget('admin_badges');
        static::saved($flush);
        static::deleted($flush);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function medecin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'repondu_par');
    }

    public function scopePubliees($q)
    {
        return $q->where('statut', 'publiee')->whereNotNull('slug')->whereNotNull('reponse');
    }

    public function getTitreAfficheAttribute(): string
    {
        return $this->titre ?: Str::limit($this->question, 120);
    }

    /** Adresse unique à partir du titre */
    public function genererSlug(): string
    {
        $base = Str::slug(Str::limit($this->titre_affiche, 80, '')) ?: 'question';
        $slug = $base;
        $i = 2;
        while (self::withTrashed()->where('slug', $slug)->where('id', '!=', $this->id)->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    /** Texte simple → paragraphes HTML échappés */
    public static function enParagraphes(?string $texte): string
    {
        $blocs = preg_split('/\R{2,}/u', trim((string) $texte));

        return collect($blocs)->filter()->map(fn($b) => '<p>' . nl2br(e(trim($b))) . '</p>')->implode("\n");
    }
}

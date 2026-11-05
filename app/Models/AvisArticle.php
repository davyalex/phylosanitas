<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Réponse à « Cet article vous a-t-il aidé ? » */
class AvisArticle extends Model
{
    protected $table = 'avis_articles';

    protected $fillable = ['post_id', 'utile', 'visiteur'];

    protected $casts = ['utile' => 'boolean'];

    /** ['oui' => n, 'non' => n] pour un article */
    public static function totaux(int $postId): array
    {
        $rows = self::where('post_id', $postId)
            ->selectRaw('utile, count(*) as n')
            ->groupBy('utile')
            ->pluck('n', 'utile');

        return ['oui' => (int) ($rows[1] ?? 0), 'non' => (int) ($rows[0] ?? 0)];
    }
}

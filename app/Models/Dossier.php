<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/** Dossier thématique : parcours d'articles dans un ordre donné */
class Dossier extends Model
{
    use SoftDeletes, Sluggable;

    protected $fillable = ['titre', 'slug', 'intro', 'actif', 'ordre'];

    protected $casts = ['actif' => 'boolean'];

    public function sluggable(): array
    {
        return ['slug' => ['source' => 'titre']];
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'dossier_post')
            ->withPivot('ordre')
            ->orderByPivot('ordre');
    }

    /** Articles publiés seulement */
    public function postsPublies(): BelongsToMany
    {
        return $this->posts()->where('posts.published', 'public');
    }

    public function scopeEnLigne($q)
    {
        return $q->where('actif', true)->whereHas('posts', fn($p) => $p->where('published', 'public'));
    }
}

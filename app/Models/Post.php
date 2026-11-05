<?php

namespace App\Models;
// use App\Models\Visits;
use Awssat\Visits\Visits;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use CyrildeWit\EloquentViewable\View;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use CyrildeWit\EloquentViewable\Visitor;
use Cviebrock\EloquentSluggable\Sluggable;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasPermissions;
// use CyrildeWit\EloquentViewable\Contracts\Visitor;

use Illuminate\Database\Eloquent\SoftDeletes;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
// use CyrildeWit\EloquentViewable\Visitor;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;




class Post extends Model implements HasMedia, Viewable
{
    use HasFactory,
        SoftDeletes,
        InteractsWithMedia,
        Sluggable,
        HasRoles,
        HasPermissions,
        InteractsWithViews;


    protected $fillable = [
        'code',
        'title',
        'meta_description',
        'slug',
        'tag',
        'description',
        'lien',
        'sources',
        'category_id',
        'user_id',
        'reviewer_id',
        'published',
        'actualite_une', // Article : « À la une » de l'accueil · Actualité : widget Actualités
        'une_at',        // date de mise à la une (le plus récent = article principal)
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'une_at' => 'datetime',
    ];

    /** Nombre de places « À la une » sur l'accueil : 1 article principal + 3 secondaires */
    public const UNE_PLACES = 4;

    /** Articles à la une de l'accueil (hors catégorie « Actualités », qui a son propre widget) */
    public function scopeALaUne($query)
    {
        $actualites = Category::where('slug', 'actualites')->value('id');

        return $query->where('actualite_une', 1)
            ->when($actualites, fn($q) => $q->where('category_id', '!=', $actualites))
            ->orderByDesc('une_at')
            ->orderByDesc('updated_at');
    }

    /**
     * Met l'article à la une (il devient l'article principal) ou l'en retire.
     * Au-delà de 4 articles, le plus ancien quitte automatiquement la une.
     * Retourne les articles qui ont quitté la une.
     */
    public function mettreALaUne(bool $on): \Illuminate\Support\Collection
    {
        if (!$on) {
            $this->forceFill(['actualite_une' => 0, 'une_at' => null])->save();
            return collect();
        }

        $this->forceFill(['actualite_une' => 1, 'une_at' => now()])->save();

        // Les actualités externes ne comptent pas dans les 4 places de l'accueil
        if ($this->category && $this->category->slug === 'actualites') return collect();

        $sortants = static::aLaUne()->where('id', '!=', $this->id)
            ->skip(self::UNE_PLACES - 1)->take(1000)->get();

        foreach ($sortants as $p) {
            $p->forceFill(['actualite_une' => 0, 'une_at' => null])->save();
        }

        return $sortants;
    }

    //slugable
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }
    /**************************** */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Médecin qui a relu l'article */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }


    /** Sources saisies une par ligne → [['texte' => …, 'url' => …|null]] */
    public function listeSources(): array
    {
        $out = [];
        foreach (preg_split('/\R/u', (string) $this->sources) as $ligne) {
            $ligne = trim($ligne, " \t-•*");
            if ($ligne === '') continue;
            preg_match('#https?://\S+#i', $ligne, $m);
            $url   = $m[0] ?? null;
            $texte = trim(str_replace($url ?? '', '', $ligne), " \t:-–—");
            $out[] = ['texte' => $texte ?: ($url ? parse_url($url, PHP_URL_HOST) : $ligne), 'url' => $url];
        }

        return $out;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function commentaires(): HasMany
    {
        return $this->hasMany(Commentaire::class);
    }


    public function optionSondages(): HasMany
    {
        return $this->hasMany(OptionSondage::class);
    }


    public function soumissions(): HasMany
    {
        return $this->hasMany(Soumission::class);
    }


    /**
     * Get the instance of the user visits
     *
     * @return Visits
     */
    public function visitsCounter()
    {
        return visits($this);
    }

    /**
     * Get the visits relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function visits()
    {
        return visits($this)->relation();
    }


    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('optimized')
            ->optimize()
            ->format(Manipulations::FORMAT_JPG);  // Optionnel: changer le format si nécessaire
            // ->quality(85);  // Ajuster la qualité

        // Miniature légère pour les cards et listes (affichées entre 100 et 420px)
        $this->addMediaConversion('thumb')
            ->fit(Manipulations::FIT_MAX, 800, 800)
            ->format(Manipulations::FORMAT_WEBP)
            ->quality(78)
            ->performOnCollections('image')
            ->nonQueued();

        // Version moyenne pour l'image de une et l'image d'article
        $this->addMediaConversion('large')
            ->fit(Manipulations::FIT_MAX, 1280, 1280)
            ->format(Manipulations::FORMAT_WEBP)
            ->quality(80)
            ->performOnCollections('image')
            ->nonQueued();
    }
}

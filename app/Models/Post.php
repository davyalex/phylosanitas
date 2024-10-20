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
        'slug',
        'tag',
        'description',
        'lien',
        'category_id',
        'user_id',
        'published',
        'actualite_une', // Mettre une actualité à la une ....mettre en slide
        'created_at',
        'updated_at',
        'deleted_at',
    ];

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

    }
}

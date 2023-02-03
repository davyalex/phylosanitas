<?php

namespace App\Models;
// use App\Models\Visits;
use Awssat\Visits\Visits;
use Spatie\MediaLibrary\HasMedia;
use CyrildeWit\EloquentViewable\View;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use CyrildeWit\EloquentViewable\Visitor;
use Cviebrock\EloquentSluggable\Sluggable;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasPermissions;
use Illuminate\Database\Eloquent\SoftDeletes;
// use CyrildeWit\EloquentViewable\Contracts\Visitor;

use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use Illuminate\Database\Eloquent\Relations\HasMany;
// use CyrildeWit\EloquentViewable\Visitor;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;




class Post extends Model implements HasMedia,Viewable
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
}

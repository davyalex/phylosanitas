<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory,
        SoftDeletes,
        Sluggable;


    protected $fillable = [
        'code',
        'slug',
        'title',
        'description',
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

    //IDGENERATOR
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->code = IdGenerator::generate([
                'table' => 'categories',
                'field' => 'code',
                'length' => 6,
                'prefix' => 'C-',
                'reset_on_prefix_change' => false,
            ]);
        });
    }


    /*************** */

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}

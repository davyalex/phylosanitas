<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;

class Actualite extends Model implements HasMedia
{
    use HasFactory,
    SoftDeletes,
        InteractsWithMedia;

        protected $fillable = [
            'title',
            'description',
            'created_at',
            'updated_at',
            'deleted_at',
        ];
}

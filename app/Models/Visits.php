<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visits extends Model
{
    use HasFactory;
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
}

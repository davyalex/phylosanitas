<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OptionSondage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'post_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }


    public function soumissions(): HasMany
    {
        return $this->hasMany(Soumission::class);
    }
    
}

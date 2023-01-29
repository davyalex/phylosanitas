<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasPermissions;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Commentaire extends Model implements HasMedia
{
    use HasFactory,
        SoftDeletes,
        InteractsWithMedia,
        HasRoles,
        HasPermissions;

    protected $fillable = [
        'message',
        'user_name',
        'user_email',
        'post_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];


    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
}

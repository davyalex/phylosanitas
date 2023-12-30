<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Soumission extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'user_session',
        'option_sondage_id',
        'post_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function post(): BelongsTo //sondage
    {
        return $this->belongsTo(Post::class, 'post_id');
    }


    public function optionSondage(): BelongsTo  //option de reponse
    {
        return $this->belongsTo(OptionSondage::class, 'option_sondage_id');
    }
}


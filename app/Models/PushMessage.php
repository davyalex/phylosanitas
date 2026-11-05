<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Notification envoyée aux abonnés */
class PushMessage extends Model
{
    protected $table = 'push_messages';

    protected $fillable = ['titre', 'corps', 'url', 'post_id', 'envoyes', 'echecs'];
}

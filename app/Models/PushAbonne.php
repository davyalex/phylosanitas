<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Appareil abonné aux notifications */
class PushAbonne extends Model
{
    protected $table = 'push_abonnes';

    protected $fillable = ['endpoint', 'endpoint_hash', 'p256dh', 'auth', 'navigateur', 'echecs'];
}

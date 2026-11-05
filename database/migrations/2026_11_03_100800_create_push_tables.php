<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Notifications sur le téléphone / l'ordinateur (Web Push) :
 *  - push_abonnes : appareils qui ont accepté les notifications ;
 *  - push_messages : notifications envoyées (le service worker lit la dernière).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('push_abonnes', function (Blueprint $table) {
            $table->id();
            $table->text('endpoint');
            $table->string('endpoint_hash', 64)->unique();
            $table->string('p256dh', 255)->nullable();
            $table->string('auth', 255)->nullable();
            $table->string('navigateur', 255)->nullable();
            $table->unsignedSmallInteger('echecs')->default(0);
            $table->timestamps();
        });

        Schema::create('push_messages', function (Blueprint $table) {
            $table->id();
            $table->string('titre', 120);
            $table->string('corps', 255)->nullable();
            $table->string('url', 500);
            $table->unsignedBigInteger('post_id')->nullable()->index();
            $table->unsignedInteger('envoyes')->default(0);
            $table->unsignedInteger('echecs')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('push_messages');
        Schema::dropIfExists('push_abonnes');
    }
};

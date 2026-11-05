<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Dossiers thématiques : plusieurs articles regroupés en un parcours ordonné
 * (ex. « Tout comprendre sur l'hypertension »).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dossiers', function (Blueprint $table) {
            $table->id();
            $table->string('titre', 150);
            $table->string('slug')->unique();
            $table->text('intro')->nullable();
            $table->boolean('actif')->default(true);
            $table->unsignedSmallInteger('ordre')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('dossier_post', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dossier_id')->constrained('dossiers')->cascadeOnDelete();
            $table->unsignedBigInteger('post_id');
            $table->unsignedSmallInteger('ordre')->default(0);

            $table->unique(['dossier_id', 'post_id']);
            $table->index('post_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dossier_post');
        Schema::dropIfExists('dossiers');
    }
};

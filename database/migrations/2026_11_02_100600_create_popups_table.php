<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Popups d'information affichés sur le site (ex. affiche « Novembre bleu »).
 * Nouvelle table : n'affecte aucune donnée existante.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('popups', function (Blueprint $table) {
            $table->id();
            $table->string('titre', 150);
            $table->text('texte')->nullable();
            $table->string('bouton_texte', 60)->nullable();
            $table->string('lien', 500)->nullable();
            $table->enum('pages', ['accueil', 'tout'])->default('tout');
            $table->enum('frequence', ['once', 'daily', 'session'])->default('daily');
            $table->unsignedTinyInteger('delai')->default(3);
            $table->boolean('actif')->default(true);
            $table->timestamp('debut_at')->nullable();
            $table->timestamp('fin_at')->nullable();
            $table->unsignedBigInteger('vues')->default(0);
            $table->unsignedBigInteger('clics')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['actif', 'debut_at', 'fin_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('popups');
    }
};

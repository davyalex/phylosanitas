<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pharmacies et leur période de garde, avec coordonnées GPS pour l'itinéraire.
 * La colonne « type » accueillera plus tard les autres établissements de l'annuaire santé.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etablissements', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 150);
            $table->string('type', 30)->default('pharmacie');
            $table->string('ville', 100)->nullable();
            $table->string('quartier', 100)->nullable();
            $table->string('adresse', 255)->nullable();
            $table->string('telephone', 40)->nullable();
            $table->string('telephone_2', 40)->nullable();
            $table->string('horaires', 150)->nullable();
            $table->string('maps_url', 500)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('notes', 255)->nullable();
            $table->date('garde_debut')->nullable();
            $table->date('garde_fin')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['actif', 'type', 'ville']);
            $table->index(['garde_debut', 'garde_fin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etablissements');
    }
};

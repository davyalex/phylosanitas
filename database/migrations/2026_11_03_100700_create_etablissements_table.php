<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Annuaire santé : pharmacies (avec période de garde), hôpitaux, cliniques,
 * centres de santé, laboratoires et numéros d'urgence.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etablissements', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 150);
            $table->enum('type', ['pharmacie', 'hopital', 'clinique', 'centre_sante', 'laboratoire', 'urgence']);
            $table->string('ville', 100)->nullable();
            $table->string('quartier', 100)->nullable();
            $table->string('adresse', 255)->nullable();
            $table->string('telephone', 40)->nullable();
            $table->string('telephone_2', 40)->nullable();
            $table->string('horaires', 150)->nullable();
            $table->string('maps_url', 500)->nullable();
            $table->string('notes', 255)->nullable();
            $table->date('garde_debut')->nullable();
            $table->date('garde_fin')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['actif', 'type', 'ville']);
            $table->index(['garde_debut', 'garde_fin']);
        });

        // Numéros d'urgence nationaux (Côte d'Ivoire) — à vérifier par l'équipe
        $now = now();
        foreach ([
            ['SAMU — urgences médicales', '185', 'Urgence médicale, malaise, accident grave'],
            ['Sapeurs-pompiers (GSPM)', '180', 'Incendie, accident, secours à personne'],
            ['Police secours', '170', 'Agression, danger immédiat'],
        ] as [$nom, $tel, $notes]) {
            DB::table('etablissements')->insert([
                'nom' => $nom, 'type' => 'urgence', 'telephone' => $tel, 'notes' => $notes,
                'horaires' => '24 h/24', 'actif' => true, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('etablissements');
    }
};

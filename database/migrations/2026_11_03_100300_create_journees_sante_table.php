<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Calendrier des journées et mois de santé (reviennent chaque année).
 * jour vide = campagne d'un mois entier (Octobre rose, Novembre bleu).
 * Pré-rempli avec les grandes journées internationales.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journees_sante', function (Blueprint $table) {
            $table->id();
            $table->string('titre', 150);
            $table->unsignedTinyInteger('jour')->nullable();
            $table->unsignedTinyInteger('mois');
            $table->string('description', 400)->nullable();
            $table->string('couleur', 7)->default('#0066cc');
            $table->string('lien', 500)->nullable();
            $table->unsignedTinyInteger('annonce_jours')->default(7);   // bandeau affiché X jours avant
            $table->boolean('actif')->default(true);
            $table->timestamps();

            $table->index(['actif', 'mois', 'jour']);
        });

        $now = now();
        $rows = [
            ['Journée mondiale contre le cancer', 4, 2, 'Prévenir, dépister tôt et mieux soigner les cancers.', '#7b3fa0'],
            ['Journée mondiale de lutte contre la tuberculose', 24, 3, 'Une toux de plus de deux semaines doit faire consulter. La tuberculose se soigne.', '#c0392b'],
            ['Journée mondiale de la santé', 7, 4, 'Journée de l\'Organisation mondiale de la santé, consacrée chaque année à un grand thème.', '#0066cc'],
            ['Journée mondiale de lutte contre le paludisme', 25, 4, 'Moustiquaire imprégnée, assainissement et dépistage rapide : ensemble contre le paludisme.', '#00a86b'],
            ['Journée mondiale sans tabac', 31, 5, 'Le tabac tue la moitié de ceux qui en consomment régulièrement. Il n\'est jamais trop tard pour arrêter.', '#5a6a80'],
            ['Journée mondiale du donneur de sang', 14, 6, 'Un don de sang peut sauver jusqu\'à trois vies.', '#c62828'],
            ['Journée mondiale contre l\'hépatite', 28, 7, 'Les hépatites B et C sont souvent silencieuses : faites-vous dépister et vacciner.', '#d68910'],
            ['Journée mondiale du cœur', 29, 9, 'Tension, sel, tabac, sédentarité : protégez votre cœur au quotidien.', '#c62828'],
            ['Octobre rose', null, 10, 'Mois de sensibilisation au dépistage du cancer du sein.', '#e75480'],
            ['Journée mondiale de la santé mentale', 10, 10, 'Parler de sa santé mentale, c\'est déjà prendre soin de soi.', '#2e86c1'],
            ['Novembre bleu', null, 11, 'Mois de sensibilisation à la santé masculine et au dépistage du cancer de la prostate.', '#1f5fae'],
            ['Journée mondiale du diabète', 14, 11, 'Un simple test de glycémie permet de dépister le diabète.', '#1f78d1'],
            ['Journée mondiale de lutte contre le sida', 1, 12, 'Se faire dépister, se protéger, accompagner les personnes vivant avec le VIH.', '#c0392b'],
        ];

        foreach ($rows as [$titre, $jour, $mois, $desc, $couleur]) {
            DB::table('journees_sante')->insert([
                'titre' => $titre, 'jour' => $jour, 'mois' => $mois, 'description' => $desc,
                'couleur' => $couleur, 'annonce_jours' => 7, 'actif' => true,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('journees_sante');
    }
};

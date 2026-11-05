<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Abécédaire des maladies : une fiche courte par maladie.
 * Les listes (symptômes, prévention…) sont saisies une ligne par élément.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maladies', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 150);
            $table->string('slug')->unique();
            $table->string('autres_noms', 255)->nullable();    // synonymes, pour la recherche
            $table->text('resume');
            $table->text('symptomes')->nullable();
            $table->text('causes')->nullable();
            $table->text('prevention')->nullable();
            $table->text('traitement')->nullable();
            $table->text('quand_consulter')->nullable();
            $table->string('mots_cles', 255)->nullable();      // pour retrouver les articles liés
            $table->boolean('publie')->default(false);
            $table->unsignedBigInteger('reviewer_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['publie', 'nom']);
        });

        // Trois fiches d'exemple, EN BROUILLON : à relire par l'équipe médicale avant publication
        $now = now();
        $fiches = [
            [
                'nom' => 'Paludisme', 'slug' => 'paludisme', 'autres_noms' => 'Malaria, palu',
                'resume' => 'Maladie due à un parasite (Plasmodium) transmis par la piqûre d\'un moustique anophèle femelle infecté. Le paludisme grave peut être mortel, surtout chez le jeune enfant et la femme enceinte.',
                'symptomes' => "Fièvre, frissons, sueurs\nMaux de tête\nCourbatures, grande fatigue\nNausées, vomissements, diarrhée",
                'causes' => "Piqûre d'un moustique anophèle infecté, surtout la nuit\nEaux stagnantes près des habitations, où les moustiques se reproduisent",
                'prevention' => "Dormir chaque nuit sous une moustiquaire imprégnée d'insecticide\nSupprimer les eaux stagnantes autour de la maison\nPorter des vêtements couvrants le soir\nFemmes enceintes : suivre le traitement préventif prescrit lors des consultations prénatales",
                'traitement' => "Le paludisme se soigne avec des médicaments antipaludiques prescrits après un test de diagnostic (TDR ou goutte épaisse). Ne prenez pas de traitement sans test.",
                'quand_consulter' => "Toute fièvre doit faire consulter rapidement, surtout chez un enfant\nUrgence : convulsions, somnolence, difficulté à respirer, vomissements répétés, urines foncées",
                'mots_cles' => 'paludisme, palu, moustiquaire',
            ],
            [
                'nom' => 'Hypertension artérielle', 'slug' => 'hypertension-arterielle', 'autres_noms' => 'HTA, tension',
                'resume' => 'La tension artérielle est trop élevée de façon durable. Souvent sans symptôme, l\'hypertension augmente le risque d\'AVC, d\'infarctus et de maladie des reins.',
                'symptomes' => "Le plus souvent : aucun symptôme\nParfois : maux de tête, vertiges, bourdonnements d'oreilles, saignements de nez",
                'causes' => "Âge et antécédents familiaux\nAlimentation trop salée, surpoids\nManque d'activité physique\nTabac, alcool, stress",
                'prevention' => "Réduire le sel (bouillons cubes, plats préparés)\nBouger au moins 30 minutes par jour\nGarder un poids raisonnable\nFaire contrôler sa tension régulièrement, surtout après 40 ans",
                'traitement' => "Changement d'hygiène de vie et, si nécessaire, médicaments à prendre tous les jours. N'arrêtez jamais un traitement sans l'avis de votre médecin.",
                'quand_consulter' => "Pour un contrôle de tension au moins une fois par an\nUrgence : douleur dans la poitrine, paralysie ou bouche déformée, trouble de la parole, maux de tête très violents",
                'mots_cles' => 'hypertension, tension, avc',
            ],
            [
                'nom' => 'Diabète', 'slug' => 'diabete', 'autres_noms' => 'Diabète de type 1, diabète de type 2, sucre dans le sang',
                'resume' => 'Le taux de sucre dans le sang (glycémie) est trop élevé. Le diabète de type 2, le plus fréquent, s\'installe souvent sans bruit pendant des années.',
                'symptomes' => "Soif intense, envie fréquente d'uriner\nFatigue, amaigrissement inexpliqué\nPlaies qui guérissent mal, infections à répétition\nVision trouble",
                'causes' => "Surpoids, manque d'activité physique\nAntécédents familiaux\nAlimentation riche en sucres et en graisses",
                'prevention' => "Manger équilibré, limiter les boissons sucrées\nBouger chaque jour\nFaire contrôler sa glycémie, surtout en cas de surpoids ou d'antécédents familiaux",
                'traitement' => "Hygiène de vie, médicaments par la bouche ou insuline selon les cas, et suivi médical régulier (yeux, pieds, reins).",
                'quand_consulter' => "En cas de soif intense ou d'amaigrissement inexpliqué\nUrgence : malaise, confusion, vomissements, respiration rapide chez une personne diabétique",
                'mots_cles' => 'diabète, glycémie, sucre',
            ],
        ];
        foreach ($fiches as $f) {
            \Illuminate\Support\Facades\DB::table('maladies')->insert($f + ['publie' => false, 'created_at' => $now, 'updated_at' => $now]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('maladies');
    }
};

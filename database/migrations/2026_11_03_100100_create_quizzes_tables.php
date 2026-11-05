<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Quiz « Vrai ou faux ? » : un quiz = une série d'affirmations, chacune vraie ou fausse,
 * avec une explication et un lien facultatif vers un article.
 * Un quiz d'exemple est créé EN BROUILLON : l'équipe médicale le relit avant de l'activer.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('titre', 150);
            $table->string('slug')->unique();
            $table->text('intro')->nullable();
            $table->boolean('actif')->default(false);
            $table->unsignedInteger('participations')->default(0);
            $table->unsignedInteger('score_total')->default(0);   // somme des bonnes réponses, pour la moyenne
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
            $table->string('affirmation', 300);
            $table->boolean('reponse');                 // true = vrai, false = faux
            $table->text('explication');
            $table->unsignedBigInteger('post_id')->nullable();   // article pour en savoir plus
            $table->unsignedSmallInteger('ordre')->default(0);
            $table->timestamps();
        });

        $now = now();
        $quizId = DB::table('quizzes')->insertGetId([
            'titre'      => 'Idées reçues sur la santé',
            'slug'       => 'idees-recues-sur-la-sante',
            'intro'      => 'Vrai ou faux ? Testez vos connaissances sur les idées reçues les plus répandues.',
            'actif'      => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $questions = [
            ['Le paludisme se transmet d\'une personne à l\'autre par simple contact.', false,
             'Le paludisme est transmis par la piqûre d\'un moustique (anophèle femelle) infecté. Il ne se transmet pas en touchant ou en côtoyant une personne malade.'],
            ['Dormir sous une moustiquaire imprégnée d\'insecticide réduit le risque de paludisme.', true,
             'La moustiquaire imprégnée est l\'un des moyens les plus efficaces de se protéger des piqûres pendant la nuit, surtout pour les jeunes enfants et les femmes enceintes.'],
            ['Les antibiotiques soignent la grippe et le rhume.', false,
             'La grippe et le rhume sont causés par des virus. Les antibiotiques n\'agissent que sur les bactéries. Les prendre sans raison favorise les résistances.'],
            ['Le diabète de type 2 peut rester longtemps sans aucun symptôme.', true,
             'Le diabète de type 2 s\'installe souvent sans signe pendant des années. Un simple contrôle de la glycémie permet de le dépister tôt.'],
            ['On peut arrêter son traitement contre l\'hypertension dès que la tension redevient normale.', false,
             'Si la tension est redevenue normale, c\'est souvent grâce au traitement. Ne l\'arrêtez jamais sans l\'avis de votre médecin.'],
            ['Se laver les mains au savon aide à prévenir les diarrhées.', true,
             'Le lavage des mains à l\'eau et au savon, surtout avant de manger et après les toilettes, réduit fortement les maladies diarrhéiques.'],
            ['Le VIH peut se transmettre par une piqûre de moustique.', false,
             'Le VIH ne se transmet pas par les moustiques. Il se transmet par le sang, les rapports sexuels non protégés et de la mère à l\'enfant.'],
            ['Pour faire baisser la fièvre d\'un enfant, il faut bien le couvrir.', false,
             'Trop couvrir un enfant fiévreux empêche la chaleur de s\'évacuer. Il faut le découvrir légèrement, le faire boire et consulter si la fièvre persiste ou s\'il paraît très malade.'],
        ];

        foreach ($questions as $i => [$affirmation, $reponse, $explication]) {
            DB::table('quiz_questions')->insert([
                'quiz_id'     => $quizId,
                'affirmation' => $affirmation,
                'reponse'     => $reponse,
                'explication' => $explication,
                'ordre'       => $i,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
        Schema::dropIfExists('quizzes');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * « Le médecin répond » : questions envoyées par les lecteurs,
 * publiées anonymement avec la réponse de l'équipe.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions_lecteurs', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100);                    // prénom ou pseudo
            $table->string('email', 150)->nullable();      // jamais affiché
            $table->string('tranche_age', 20)->nullable();
            $table->text('question');
            $table->string('titre', 200)->nullable();      // question reformulée pour la publication
            $table->string('slug')->nullable()->unique();
            $table->longText('reponse')->nullable();
            $table->enum('statut', ['nouvelle', 'publiee', 'rejetee'])->default('nouvelle');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('repondu_par')->nullable();
            $table->timestamp('repondu_at')->nullable();
            $table->unsignedInteger('vues')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['statut', 'repondu_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions_lecteurs');
    }
};

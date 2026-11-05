<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * « Cet article vous a-t-il aidé ? » : un avis (oui / non) par visiteur et par article.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('avis_articles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id');
            $table->boolean('utile');
            $table->string('visiteur', 64);   // empreinte anonyme (session), pas de donnée personnelle
            $table->timestamps();

            $table->unique(['post_id', 'visiteur']);
            $table->index(['post_id', 'utile']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avis_articles');
    }
};

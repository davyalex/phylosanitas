<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publicites', function (Blueprint $table) {
            $table->id();
            $table->string('titre', 150);
            $table->string('annonceur', 150)->nullable();
            $table->enum('emplacement', ['leaderboard', 'rectangle', 'infeed', 'inarticle']);
            $table->enum('type', ['image', 'code'])->default('image');
            $table->string('lien', 500)->nullable();
            $table->text('code')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamp('debut_at')->nullable();
            $table->timestamp('fin_at')->nullable();
            $table->unsignedSmallInteger('priorite')->default(1);
            $table->unsignedBigInteger('impressions')->default(0);
            $table->unsignedBigInteger('clics')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['emplacement', 'actif']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publicites');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('actualites', function (Blueprint $table) {
            $table->string('sous_titre', 300)->nullable()->after('title');
            $table->string('lien', 500)->nullable()->after('sous_titre');
            $table->string('texte_bouton', 60)->nullable()->default('Lire article')->after('lien');
            $table->boolean('actif')->default(true)->after('texte_bouton');
            $table->unsignedSmallInteger('ordre')->default(0)->after('actif');
        });
    }

    public function down(): void
    {
        Schema::table('actualites', function (Blueprint $table) {
            $table->dropColumn(['sous_titre', 'lien', 'texte_bouton', 'actif', 'ordre']);
        });
    }
};

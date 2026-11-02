<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Date de mise « À la une » : sert à ordonner la une de l'accueil
 * (le plus récent devient l'article principal).
 * Ajoute une colonne vide : aucune donnée existante n'est modifiée,
 * sauf la date des articles déjà à la une (reprise de leur date de modification).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('posts', 'une_at')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->timestamp('une_at')->nullable()->after('actualite_une');
            });
        }

        DB::table('posts')->where('actualite_une', 1)->whereNull('une_at')
            ->update(['une_at' => DB::raw('COALESCE(updated_at, created_at)')]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('posts', 'une_at')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropColumn('une_at');
            });
        }
    }
};

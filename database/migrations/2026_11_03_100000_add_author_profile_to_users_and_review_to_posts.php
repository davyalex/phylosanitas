<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Crédibilité médicale :
 *  - profil public des auteurs (fonction, biographie, photo, page « auteur ») ;
 *  - « Relu par » et sources sur les articles.
 * Ajoute uniquement des colonnes facultatives : aucune donnée existante n'est modifiée.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
            $table->string('titre_pro', 120)->nullable()->after('slug');   // ex. « Médecin généraliste »
            $table->text('bio')->nullable()->after('titre_pro');
            $table->string('photo')->nullable()->after('bio');
            $table->boolean('est_medecin')->default(false)->after('photo');
            $table->boolean('profil_public')->default(false)->after('est_medecin');
        });

        // Adresse de page auteur pour les comptes existants
        foreach (DB::table('users')->select('id', 'name')->get() as $u) {
            $base = Str::slug(Str::before($u->name, '@')) ?: 'auteur';
            $slug = $base;
            $i = 2;
            while (DB::table('users')->where('slug', $slug)->exists()) $slug = $base . '-' . $i++;
            DB::table('users')->where('id', $u->id)->update(['slug' => $slug]);
        }

        Schema::table('posts', function (Blueprint $table) {
            $table->unsignedBigInteger('reviewer_id')->nullable()->after('user_id');
            $table->text('sources')->nullable()->after('lien');
            $table->index('reviewer_id');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex(['reviewer_id']);
            $table->dropColumn(['reviewer_id', 'sources']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn(['slug', 'titre_pro', 'bio', 'photo', 'est_medecin', 'profil_public']);
        });
    }
};

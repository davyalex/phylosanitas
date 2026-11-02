<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key', 100)->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Valeurs de départ = informations affichées jusqu'ici sur le site
        $now = now();
        $defaults = [
            'site_nom'        => 'PhyloSanitas',
            'slogan'          => 'Votre santé, notre priorité',
            'description'     => "Votre référence santé en Côte d'Ivoire. Articles médicaux, actualités santé, sondages et conseils bien-être.",
            'adresse'         => "Abidjan, Côte d'Ivoire",
            'ville'           => 'Abidjan',
            'pays_code'       => 'CI',
            'telephone_1'     => '(+225) 01 03 48 75 55',
            'telephone_1_label' => 'Standard',
            'email_1'         => 'dr_akencho@yahoo.fr',
            'email_1_label'   => 'Contact',
            'facebook'        => 'https://www.facebook.com/phylosanitas',
            'afficher_visites'=> '1',
            'avertissement'   => "Les contenus publiés sur ce site sont fournis à titre d'information et ne remplacent pas l'avis d'un professionnel de santé. En cas de doute ou d'urgence, consultez un médecin.",
        ];
        DB::table('settings')->insert(array_map(
            fn($k, $v) => ['key' => $k, 'value' => $v, 'created_at' => $now, 'updated_at' => $now],
            array_keys($defaults), $defaults
        ));
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};

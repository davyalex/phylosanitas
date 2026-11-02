<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('categories')->delete();
        
        \DB::table('categories')->insert(array (
            0 => 
            array (
                'id' => 51,
                'code' => 'C-0036',
                'slug' => 'sante-public',
                'title' => 'Santé publique',
                'description' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2023-01-27 00:53:54',
                'updated_at' => '2023-02-11 17:25:25',
            ),
            1 => 
            array (
                'id' => 52,
                'code' => 'C-0037',
                'slug' => 'theses-soutenues',
                'title' => 'Thèses',
                'description' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2023-01-27 00:54:25',
                'updated_at' => '2023-02-11 17:26:01',
            ),
            2 => 
            array (
                'id' => 53,
                'code' => 'C-0038',
                'slug' => 'bon-a-savoir',
                'title' => 'Bon à savoir',
                'description' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2023-01-27 00:54:40',
                'updated_at' => '2023-02-11 17:26:19',
            ),
            3 => 
            array (
                'id' => 57,
                'code' => 'C-0042',
                'slug' => 'sondage',
                'title' => 'Sondage',
                'description' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2023-02-11 17:28:55',
                'updated_at' => '2023-02-19 13:57:10',
            ),
            4 => 
            array (
                'id' => 58,
                'code' => 'C-0043',
                'slug' => 'actualites',
                'title' => 'Actualités',
                'description' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-02-10 03:31:29',
                'updated_at' => '2024-02-23 13:12:45',
            ),
            5 => 
            array (
                'id' => 59,
                'code' => 'C-0044',
                'slug' => 'scholar',
                'title' => 'Scolaire',
                'description' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2024-09-22 09:00:44',
                'updated_at' => '2024-09-22 09:58:37',
            ),
            6 => 
            array (
                'id' => 60,
                'code' => 'C-0045',
                'slug' => 'obesite',
                'title' => 'Obésité',
                'description' => NULL,
                'deleted_at' => NULL,
                'created_at' => '2025-06-15 09:58:10',
                'updated_at' => '2025-06-15 09:58:10',
            ),
        ));
        
        
    }
}
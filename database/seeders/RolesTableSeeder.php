<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('roles')->delete();
        
        \DB::table('roles')->insert(array (
            0 => 
            array (
                'created_at' => '2023-01-27 14:10:30',
                'guard_name' => 'web',
                'id' => 1,
                'name' => 'administrateur',
                'updated_at' => '2023-01-27 14:10:30',
            ),
            1 => 
            array (
                'created_at' => '2023-01-27 14:10:30',
                'guard_name' => 'web',
                'id' => 2,
                'name' => 'webmaster',
                'updated_at' => '2023-01-27 14:10:30',
            ),
        ));
        
        
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('users')->delete();
        
        \DB::table('users')->insert(array (
            0 => 
            array (
                'active' => 'yes',
                'created_at' => '2025-12-17 10:58:04',
                'deleted_at' => NULL,
                'email' => 'admin@phylosanitas.com',
                'email_verified_at' => NULL,
                'id' => 1,
                'name' => 'Admin',
                'password' => '$2y$10$z499S4kBszy7W7EIscNihuN2Y.jrFKq7uTW53tJAeqImq.9nFIuXC',
                'phone' => NULL,
                'remember_token' => NULL,
                'role' => NULL,
                'updated_at' => '2025-12-17 10:58:04',
            ),
            1 => 
            array (
                'active' => 'yes',
                'created_at' => '2023-01-27 16:44:53',
                'deleted_at' => NULL,
                'email' => 'phylosanitas@webmaster',
                'email_verified_at' => NULL,
                'id' => 2,
                'name' => 'phylosanitas@webmaster',
                'password' => '$2y$10$mUxS5QLKj2ziaaIx1YCPyuEZpV.CBRtgBVouRfLSixAa98.jCO7Im',
                'phone' => '00000000',
                'remember_token' => NULL,
                'role' => 'webmaster',
                'updated_at' => '2023-01-27 19:36:02',
            ),
            2 => 
            array (
                'active' => 'yes',
                'created_at' => '2023-01-27 19:42:26',
                'deleted_at' => NULL,
                'email' => 'phylosanitas@admin',
                'email_verified_at' => NULL,
                'id' => 3,
                'name' => 'phylosanitas@admin',
                'password' => '$2y$10$/zhiKHCbFmVJhKQA3ifD5uwCz1daM2.LrBwk.hcb1G7cvoSey4/re',
                'phone' => '12345678',
                'remember_token' => NULL,
                'role' => 'administrateur',
                'updated_at' => '2023-02-03 22:39:12',
            ),
            3 => 
            array (
                'active' => 'yes',
                'created_at' => '2023-02-03 23:00:47',
                'deleted_at' => NULL,
                'email' => 'devdav@gmail.com',
                'email_verified_at' => NULL,
                'id' => 5,
                'name' => 'devdav',
                'password' => '$2y$10$4NfWVS1cx796yV37CoPXJugHfzL13rh9p05g7A6mwfMNPNY1GRBoi',
                'phone' => '0779613593',
                'remember_token' => NULL,
                'role' => 'administrateur',
                'updated_at' => '2023-02-03 23:00:47',
            ),
        ));
        
        
    }
}
<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Hashing the passwords with the correct algorithm (PASSWORD_BCRYPT)
        $passwordAdmin = password_hash('123', PASSWORD_BCRYPT);
        $passwordSchool = password_hash('123', PASSWORD_BCRYPT);

        // Get the database connection (you only need to do this once)
        $db = \Config\Database::connect();

        // Insert the admin user
        $db->table('users')->insert([
            'username' => 'admin1',
            'password' => $passwordAdmin,
            'role' => 'scholar_admin',
            'school_id' => null,
        ]);

        // Insert the school admin user
        $db->table('users')->insert([
            'username' => 'school1',
            'password' => $passwordSchool,
            'role' => 'school_admin',
            'school_id' => null,
        ]);
    }
}

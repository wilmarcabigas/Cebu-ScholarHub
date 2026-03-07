<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Strong passwords
        $passwordAdmin = password_hash('Admin@1234', PASSWORD_BCRYPT);   // strong password
        $passwordSchool = password_hash('School@1234', PASSWORD_BCRYPT); // strong password

        // Get the database connection
        $db = \Config\Database::connect();

        // Check if admin already exists
        $existingAdmin = $db->table('users')->getWhere(['username' => 'admin1'])->getRowArray();
        if ($existingAdmin) {
            // Update existing admin password
            $db->table('users')->update(
                ['password' => $passwordAdmin],
                ['id' => $existingAdmin['id']]
            );
            echo "Admin password updated successfully.\n";
        } else {
            // Insert new admin user
            $db->table('users')->insert([
                'username' => 'admin1',
                'password' => $passwordAdmin,
                'role' => 'scholar_admin',
                'school_id' => null,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            echo "Admin account created successfully.\n";
        }

        // Check if school admin exists
        $existingSchoolAdmin = $db->table('users')->getWhere(['username' => 'school1'])->getRowArray();
        if ($existingSchoolAdmin) {
            $db->table('users')->update(
                ['password' => $passwordSchool],
                ['id' => $existingSchoolAdmin['id']]
            );
            echo "School admin password updated successfully.\n";
        } else {
            // Insert new school admin
            $db->table('users')->insert([
                'username' => 'school1',
                'password' => $passwordSchool,
                'role' => 'school_admin',
                'school_id' => null,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            echo "School admin account created successfully.\n";
        }
    }
}
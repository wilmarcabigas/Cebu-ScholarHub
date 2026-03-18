<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'email'         => 'superadmin@cebu-scholar.gov',
            'password_hash' => password_hash('secret123', PASSWORD_DEFAULT),
            'full_name'     => 'Super Administrator',
            'role'          => 'super_admin',
            'school_id'     => null,
            'status'        => 'active',
            'created_at'    => Time::now(),
        ];

        $this->db->table('users')->insert($data);
    }
}
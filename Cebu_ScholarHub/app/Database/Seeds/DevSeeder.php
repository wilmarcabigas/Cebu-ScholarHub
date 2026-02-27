<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;
class DevSeeder extends Seeder
{
    public function run()
    {
        // Insert a demo school
        $this->db->table('schools')->insert([
            'name'       => 'Cebu Eastern College',
            'code'       => 'CEC',
            'created_at' => Time::now(),
        ]);

        $school = $this->db->table('schools')->getWhere(['code' => 'CEC'])->getRow();

        $users = [
            [
                'email'         => 'admin@cebu-scholar.gov',
                'password_hash' => password_hash('secret123', PASSWORD_DEFAULT),
                'full_name'     => 'System Admin',
                'role'          => 'admin',
                'school_id'     => null,
                'status'        => 'active',
                'created_at'    => Time::now(),
            ],
            [
                'email'         => 'staff@cebu-scholar.gov',
                'password_hash' => password_hash('secret123', PASSWORD_DEFAULT),
                'full_name'     => 'Office Staff',
                'role'          => 'staff',
                'school_id'     => null,
                'status'        => 'active',
                'created_at'    => Time::now(),
            ],
            [
                'email'         => 'schooladmin@cec.edu.ph',
                'password_hash' => password_hash('secret123', PASSWORD_DEFAULT),
                'full_name'     => 'CEC School Admin',
                'role'          => 'school_admin',
                'school_id'     => $school->id ?? null,
                'status'        => 'active',
                'created_at'    => Time::now(),
            ],
            [
                'email'         => 'schoolstaff@cec.edu.ph',
                'password_hash' => password_hash('secret123', PASSWORD_DEFAULT),
                'full_name'     => 'CEC School Staff',
                'role'          => 'school_staff',
                'school_id'     => $school->id ?? null,
                'status'        => 'active',
                'created_at'    => Time::now(),
            ],
            [
                'email'         => 'scholar1@students.ph',
                'password_hash' => password_hash('secret123', PASSWORD_DEFAULT),
                'full_name'     => 'Juan Dela Cruz',
                'role'          => 'scholar',
                'school_id'     => null,
                'status'        => 'active',
                'created_at'    => Time::now(),
            ],
        ];

        $this->db->table('users')->insertBatch($users);
    }
}
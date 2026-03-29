<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class DevSeeder extends Seeder
{
    public function run()
    {
        $db = $this->db;

        // Insert or update demo school
        $schoolData = [
            'name'       => 'Cebu Eastern College',
            'code'       => 'CEC',
            'created_at' => Time::now(),
        ];

        $existingSchool = $db->table('schools')->getWhere(['code' => $schoolData['code']])->getRow();

        if ($existingSchool) {
            $db->table('schools')->where('id', $existingSchool->id)->update($schoolData);
            $schoolId = $existingSchool->id;
        } else {
            $db->table('schools')->insert($schoolData);
            $schoolId = $db->insertID();
        }

        // Users array with strong passwords
        $users = [
            [
                'email'         => 'jamestrocio842@gmail.com',
                'password_hash' => password_hash('Admin@2026!', PASSWORD_DEFAULT),
                'full_name'     => 'System Admin',
                'role'          => 'admin',
                'school_id'     => null,
                'status'        => 'active',
                'created_at'    => Time::now(),
            ],
            [
                'email'         => 'kenjie.manego@gmail.com',
                'password_hash' => password_hash('Admin@2026!', PASSWORD_DEFAULT),
                'full_name'     => 'System Admin 2cd',
                'role'          => 'admin',
                'school_id'     => null,
                'status'        => 'active',
                'created_at'    => Time::now(),
            ],
            [
                'email'         => 'trocioj914@gmail.com',
                'password_hash' => password_hash('Staff@2026!', PASSWORD_DEFAULT),
                'full_name'     => 'Office Staff',
                'role'          => 'staff',
                'school_id'     => null,
                'status'        => 'active',
                'created_at'    => Time::now(),
            ],
            [
                'email'         => 'schooladmin@cec.edu.ph',
                'password_hash' => password_hash('SchoolAdmin@2026!', PASSWORD_DEFAULT),
                'full_name'     => 'CEC School Admin',
                'role'          => 'school_admin',
                'school_id'     => $schoolId,
                'status'        => 'active',
                'created_at'    => Time::now(),
            ],
            [
                'email'         => 'schoolstaff@cec.edu.ph',
                'password_hash' => password_hash('SchoolStaff@2026!', PASSWORD_DEFAULT),
                'full_name'     => 'CEC School Staff',
                'role'          => 'school_staff',
                'school_id'     => $schoolId,
                'status'        => 'active',
                'created_at'    => Time::now(),
            ],
            [
                'email'         => 'scholar1@students.ph',
                'password_hash' => password_hash('Scholar@2026!', PASSWORD_DEFAULT),
                'full_name'     => 'Juan Dela Cruz',
                'role'          => 'scholar',
                'school_id'     => null,
                'status'        => 'active',
                'created_at'    => Time::now(),
            ],
        ];

        // Insert or update each user
        foreach ($users as $user) {
            $existingUser = $db->table('users')->getWhere(['email' => $user['email']])->getRow();

            if ($existingUser) {
                $db->table('users')->where('id', $existingUser->id)->update($user);
            } else {
                $db->table('users')->insert($user);
            }
        }
    }
}
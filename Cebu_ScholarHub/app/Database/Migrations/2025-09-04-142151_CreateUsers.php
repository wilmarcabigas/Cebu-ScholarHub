<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsers extends Migration
{
     public function up()
    {
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'email'          => ['type' => 'VARCHAR', 'constraint' => 191, 'unique' => true],
            'password_hash'  => ['type' => 'VARCHAR', 'constraint' => 255],
            'full_name'      => ['type' => 'VARCHAR', 'constraint' => 191],
            // Single-role approach (simple RBAC). Values: admin, staff, school_admin, school_staff, scholar
            'role'           => ['type' => 'VARCHAR', 'constraint' => 32, 'default' => 'staff'],
            // If user belongs to a school (for school_* roles)
            'school_id'      => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'status'         => ['type' => 'VARCHAR', 'constraint' => 16, 'default' => 'active'], // active|disabled
            'last_login_at'  => ['type' => 'DATETIME', 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('school_id', 'schools', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}

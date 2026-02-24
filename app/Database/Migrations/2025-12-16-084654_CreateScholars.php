<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateScholars extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'school_id' => ['type' => 'INT', 'unsigned' => true],
            'first_name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'last_name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'middle_name' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'gender' => [
                'type' => 'ENUM',
                'constraint' => ['male','female','other'],
            ],
            'course' => ['type' => 'VARCHAR', 'constraint' => 100],
            'year_level' => ['type' => 'INT'],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active','on-hold','graduated','disqualified'],
            ],
            'date_of_birth' => ['type' => 'DATE'],
            'email' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'created_at' => ['type' => 'DATETIME'],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('school_id', 'schools', 'id');

        $this->forge->createTable('scholars');
    }

    public function down()
    {
        $this->forge->dropTable('scholars');
    }
}

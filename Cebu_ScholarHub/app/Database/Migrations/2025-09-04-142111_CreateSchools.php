<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSchools extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 191],
            'code'        => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('schools');
    }

    public function down()
    {
        $this->forge->dropTable('schools');
    }
}


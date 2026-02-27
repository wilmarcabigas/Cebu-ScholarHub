<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSchools extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 191,
            ],
            'code' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'address' => [
                'type' => 'TEXT',
            ],
            'contact_person' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'contact_email' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'contact_number' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('schools');
    }

    public function down()
    {
        $this->forge->dropTable('schools');
    }
}

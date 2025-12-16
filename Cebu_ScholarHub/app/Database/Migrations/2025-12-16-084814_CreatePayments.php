<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePayments extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'bill_id' => ['type' => 'INT', 'unsigned' => true],
            'status_update' => [
                'type' => 'ENUM',
                'constraint' => ['pending','paid','overdue'],
            ],
            'updated_by' => ['type' => 'INT', 'unsigned' => true],
            'update_date' => ['type' => 'DATETIME'],
            'remarks' => ['type' => 'TEXT', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('bill_id', 'bills', 'id');
        $this->forge->addForeignKey('updated_by', 'users', 'id');

        $this->forge->createTable('payments');
    }

    public function down()
    {
        $this->forge->dropTable('payments');
    }
}

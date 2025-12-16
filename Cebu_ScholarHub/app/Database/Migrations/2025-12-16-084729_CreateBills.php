<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBills extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'scholar_id' => ['type' => 'INT', 'unsigned' => true],
            'billing_period' => ['type' => 'VARCHAR', 'constraint' => 50],
            'amount_due' => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'due_date' => ['type' => 'DATE'],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending','paid','overdue'],
                'default' => 'pending',
            ],
            'remarks' => ['type' => 'TEXT', 'null' => true],
            'posted_by' => ['type' => 'INT', 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME'],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('scholar_id', 'scholars', 'id');
        $this->forge->addForeignKey('posted_by', 'users', 'id');

        $this->forge->createTable('bills');
    }

    public function down()
    {
        $this->forge->dropTable('bills');
    }
}

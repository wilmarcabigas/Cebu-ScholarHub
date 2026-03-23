<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddScholarshipTypeToScholars extends Migration
{
    public function up()
    {
        $this->forge->addColumn('scholars', [
            'scholarship_type' => [
                'type'       => 'ENUM',
                'constraint' => ['4_semester', '8_semester', '10_semester'],
                'default'    => '4_semester',
                'null'       => false,
                'after'      => 'semesters_acquired',
            ],
            'upgraded_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'scholarship_type',
            ],
            'upgraded_by' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
                'after'    => 'upgraded_at',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('scholars', ['scholarship_type', 'upgraded_at', 'upgraded_by']);
    }
}

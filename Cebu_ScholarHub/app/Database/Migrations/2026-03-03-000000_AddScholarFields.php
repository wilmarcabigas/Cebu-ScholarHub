<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddScholarFields extends Migration
{
    public function up()
    {
        $this->forge->addColumn('scholars', [
            'semesters_acquired' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
                'default' => 1,
                'after' => 'status',
            ],
            'voucher_no' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
                'unique' => true,
                'after' => 'semesters_acquired',
            ],
            'name_extension' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false,
                'default' => '',
                'after' => 'voucher_no',
            ],
            'address' => [
                'type' => 'TEXT',
                'null' => false,
                'after' => 'name_extension',
            ],
            'contact_no' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false,
                'after' => 'address',
            ],
            'lrn_no' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false,
                'unique' => true,
                'after' => 'contact_no',
            ],
            'school_elementary' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
                'after' => 'lrn_no',
            ],
            'school_junior' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
                'after' => 'school_elementary',
            ],
            'school_senior_high' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
                'after' => 'school_junior',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('scholars', [
            'semesters_acquired',
            'voucher_no',
            'name_extension',
            'address',
            'contact_no',
            'lrn_no',
            'school_elementary',
            'school_junior',
            'school_senior_high',
        ]);
    }
}

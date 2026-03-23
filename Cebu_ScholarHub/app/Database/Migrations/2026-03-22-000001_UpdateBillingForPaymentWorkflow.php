<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateBillingForPaymentWorkflow extends Migration
{
    public function up(): void
    {
        // Add voucher_no to payments table
        $this->forge->addColumn('payments', [
            'voucher_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'after'      => 'remarks',
            ],
        ]);

        // Add rejection_remarks, receipt_confirmed_at, receipt_confirmed_by to billing_batches
        $this->forge->addColumn('billing_batches', [
            'rejection_remarks' => [
                'type'    => 'TEXT',
                'null'    => true,
                'default' => null,
                'after'   => 'remarks',
            ],
            'receipt_confirmed_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
                'after'   => 'rejection_remarks',
            ],
            'receipt_confirmed_by' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => true,
                'default'    => null,
                'after'      => 'receipt_confirmed_at',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('payments', 'voucher_no');
        $this->forge->dropColumn('billing_batches', [
            'rejection_remarks',
            'receipt_confirmed_at',
            'receipt_confirmed_by',
        ]);
    }
}
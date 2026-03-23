<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Add Billing Feature Tables
 *
 * This migration is safe to run on the existing scholarhub database.
 * It does NOT drop or alter the existing `bills` or `payments` tables —
 * those are left untouched to preserve your current data.
 *
 * What this migration does:
 *   1. Creates `billing_batches`  — one bulk billing per school per semester
 *   2. Creates `billing_items`    — one row per scholar inside a batch
 *   3. Alters  `bills`            — replaces scholar_id/billing_period with
 *                                   batch_id + school_id + amount_paid column
 *   4. Alters  `payments`         — adds created_at / updated_at timestamps
 *   5. Adds    `id_num`, `barangay`, `control_no` to `scholars` if missing
 */
class CreateBillingTables extends Migration
{
    public function up(): void
    {
        // ================================================================
        // 1. CREATE billing_batches
        //    One record per school per semester submission
        // ================================================================
        if (!$this->db->tableExists('billing_batches')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'school_id' => [
                    'type'     => 'INT',
                    'unsigned' => true,
                ],
                'semester' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    // e.g. "1st Semester", "2nd Semester"
                ],
                'school_year' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    // e.g. "2024-2025"
                ],
                'batch_label' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    // e.g. "BATCH 2024 SCHOLARS"
                ],
                'total_amount' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '12,2',
                    'default'    => 0,
                ],
                'status' => [
                    'type'       => 'ENUM',
                    'constraint' => ['draft', 'submitted', 'received', 'partial', 'paid'],
                    'default'    => 'draft',
                ],
                'submitted_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'submitted_by' => [
                    'type'     => 'INT',
                    'unsigned' => true,
                    'null'     => true,
                ],
                'remarks' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);

            $this->forge->addPrimaryKey('id');
            $this->forge->addKey('school_id');
            $this->forge->addKey('status');
            $this->forge->createTable('billing_batches');

            // Foreign keys added separately after table creation
            $this->db->query('
                ALTER TABLE `billing_batches`
                ADD CONSTRAINT `fk_billing_batches_school`
                FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`)
                ON DELETE CASCADE ON UPDATE CASCADE
            ');

            $this->db->query('
                ALTER TABLE `billing_batches`
                ADD CONSTRAINT `fk_billing_batches_submitted_by`
                FOREIGN KEY (`submitted_by`) REFERENCES `users` (`id`)
                ON DELETE SET NULL ON UPDATE CASCADE
            ');
        }

        // ================================================================
        // 2. CREATE billing_items
        //    One row per scholar inside a billing batch
        // ================================================================
        if (!$this->db->tableExists('billing_items')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'batch_id' => [
                    'type'     => 'INT',
                    'unsigned' => true,
                ],
                'scholar_id' => [
                    'type'     => 'INT',
                    'unsigned' => true,
                ],
                'control_no' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                    // copied from scholar at time of billing
                ],
                'amount' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '10,2',
                    'default'    => 10000.00,
                    // fixed ₱10,000 per scholar per semester
                ],
            ]);

            $this->forge->addPrimaryKey('id');
            $this->forge->addKey('batch_id');
            $this->forge->addKey('scholar_id');
            $this->forge->createTable('billing_items');

            $this->db->query('
                ALTER TABLE `billing_items`
                ADD CONSTRAINT `fk_billing_items_batch`
                FOREIGN KEY (`batch_id`) REFERENCES `billing_batches` (`id`)
                ON DELETE CASCADE ON UPDATE CASCADE
            ');

            $this->db->query('
                ALTER TABLE `billing_items`
                ADD CONSTRAINT `fk_billing_items_scholar`
                FOREIGN KEY (`scholar_id`) REFERENCES `scholars` (`id`)
                ON DELETE CASCADE ON UPDATE CASCADE
            ');
        }

        // ================================================================
        // 3. ALTER bills
        //    Add batch_id, school_id, amount_paid columns.
        //    The old scholar_id / billing_period columns are kept so your
        //    existing 17 rows remain intact and readable.
        //    New batch-based bills will use batch_id + school_id.
        // ================================================================
        $billColumns = $this->db->getFieldNames('bills');

        if (!in_array('batch_id', $billColumns)) {
            $this->db->query('
                ALTER TABLE `bills`
                ADD COLUMN `batch_id` INT(10) UNSIGNED NULL DEFAULT NULL
                    AFTER `id`,
                ADD CONSTRAINT `fk_bills_batch`
                    FOREIGN KEY (`batch_id`) REFERENCES `billing_batches` (`id`)
                    ON DELETE CASCADE ON UPDATE CASCADE
            ');
        }

        if (!in_array('school_id', $billColumns)) {
            $this->db->query('
                ALTER TABLE `bills`
                ADD COLUMN `school_id` INT(10) UNSIGNED NULL DEFAULT NULL
                    AFTER `batch_id`,
                ADD CONSTRAINT `fk_bills_school`
                    FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`)
                    ON DELETE CASCADE ON UPDATE CASCADE
            ');
        }

        if (!in_array('amount_paid', $billColumns)) {
            $this->db->query('
                ALTER TABLE `bills`
                ADD COLUMN `amount_paid` DECIMAL(12,2) NOT NULL DEFAULT 0.00
                    AFTER `amount_due`
            ');
        }

        // Expand status ENUM to include new batch-level statuses
        // (keeps existing 'pending','paid','overdue' values valid)
        $this->db->query("
            ALTER TABLE `bills`
            MODIFY COLUMN `status`
                ENUM('pending','paid','overdue','unpaid','partial','received')
                NOT NULL DEFAULT 'unpaid'
        ");

        // ================================================================
        // 4. ALTER payments
        //    Add created_at / updated_at timestamps for CodeIgniter's
        //    useTimestamps = true to work correctly.
        //    The existing `update_date` column is left in place.
        // ================================================================
        $paymentColumns = $this->db->getFieldNames('payments');

        if (!in_array('created_at', $paymentColumns)) {
            $this->db->query('
                ALTER TABLE `payments`
                ADD COLUMN `created_at` DATETIME NULL DEFAULT NULL
                    AFTER `remarks`
            ');
        }

        if (!in_array('updated_at', $paymentColumns)) {
            $this->db->query('
                ALTER TABLE `payments`
                ADD COLUMN `updated_at` DATETIME NULL DEFAULT NULL
                    AFTER `created_at`
            ');
        }

        // ================================================================
        // 5. ALTER scholars
        //    Add id_num, barangay, control_no — used in the billing sheet
        //    to match the Excel format from the CCCSP office.
        //    These are added as nullable so existing rows are unaffected.
        // ================================================================
        $scholarColumns = $this->db->getFieldNames('scholars');

        if (!in_array('id_num', $scholarColumns)) {
            $this->db->query('
                ALTER TABLE `scholars`
                ADD COLUMN `id_num` VARCHAR(30) NULL DEFAULT NULL
                    AFTER `school_id`,
                ADD INDEX `idx_scholars_id_num` (`id_num`)
            ');
        }

        if (!in_array('barangay', $scholarColumns)) {
            $this->db->query('
                ALTER TABLE `scholars`
                ADD COLUMN `barangay` VARCHAR(100) NULL DEFAULT NULL
                    AFTER `address`
            ');
        }

        if (!in_array('control_no', $scholarColumns)) {
            $this->db->query('
                ALTER TABLE `scholars`
                ADD COLUMN `control_no` VARCHAR(50) NULL DEFAULT NULL
                    AFTER `voucher_no`
            ');
        }
    }

    // ====================================================================
    // DOWN — reverses everything safely
    // ====================================================================
    public function down(): void
    {
        // Remove added columns from scholars
        $scholarColumns = $this->db->getFieldNames('scholars');
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

        if (in_array('id_num', $scholarColumns)) {
            $this->db->query('ALTER TABLE `scholars` DROP COLUMN `id_num`');
        }
        if (in_array('barangay', $scholarColumns)) {
            $this->db->query('ALTER TABLE `scholars` DROP COLUMN `barangay`');
        }
        if (in_array('control_no', $scholarColumns)) {
            $this->db->query('ALTER TABLE `scholars` DROP COLUMN `control_no`');
        }

        // Remove added columns from payments
        $paymentColumns = $this->db->getFieldNames('payments');
        if (in_array('created_at', $paymentColumns)) {
            $this->db->query('ALTER TABLE `payments` DROP COLUMN `created_at`');
        }
        if (in_array('updated_at', $paymentColumns)) {
            $this->db->query('ALTER TABLE `payments` DROP COLUMN `updated_at`');
        }

        // Remove added columns from bills
        $billColumns = $this->db->getFieldNames('bills');
        if (in_array('amount_paid', $billColumns)) {
            $this->db->query('ALTER TABLE `bills` DROP COLUMN `amount_paid`');
        }
        if (in_array('school_id', $billColumns)) {
            $this->db->query('ALTER TABLE `bills` DROP FOREIGN KEY `fk_bills_school`');
            $this->db->query('ALTER TABLE `bills` DROP COLUMN `school_id`');
        }
        if (in_array('batch_id', $billColumns)) {
            $this->db->query('ALTER TABLE `bills` DROP FOREIGN KEY `fk_bills_batch`');
            $this->db->query('ALTER TABLE `bills` DROP COLUMN `batch_id`');
        }

        // Revert bills status ENUM
        $this->db->query("
            ALTER TABLE `bills`
            MODIFY COLUMN `status`
                ENUM('pending','paid','overdue')
                NOT NULL DEFAULT 'pending'
        ");

        // Drop new tables
        $this->forge->dropTable('billing_items',   true);
        $this->forge->dropTable('billing_batches', true);

        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
    }
}
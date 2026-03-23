<?php

namespace App\Models;

use CodeIgniter\Model;

class BillingItemModel extends Model
{
    protected $table      = 'billing_items';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'batch_id',
        'scholar_id',
        'control_no',
        'amount',        // fixed 10000 by default
    ];

    protected $useTimestamps = false;

    // ----------------------------------------------------------------
    // Fetch all items in a batch with scholar details joined
    // ----------------------------------------------------------------
    public function getItemsWithScholars(int $batchId): array
    {
        return $this->select(
                'billing_items.id,
                 billing_items.batch_id,
                 billing_items.scholar_id,
                 billing_items.control_no,
                 billing_items.amount,
                 scholars.id_num,
                 scholars.first_name,
                 scholars.middle_name,
                 scholars.last_name,
                 scholars.course,
                 scholars.year_level,
                 scholars.barangay,
                 scholars.address,
                 scholars.control_no AS scholar_control_no,
                 schools.name AS school_name'
            )
            ->join('scholars', 'scholars.id = billing_items.scholar_id')
            ->join('schools',  'schools.id  = scholars.school_id')
            ->where('billing_items.batch_id', $batchId)
            ->orderBy('scholars.last_name', 'ASC')
            ->findAll();
    }

    // Delete all items for a batch (used when re-drafting)
    public function deleteByBatch(int $batchId): void
    {
        $this->where('batch_id', $batchId)->delete();
    }

    // Sum of all item amounts in a batch
    public function sumByBatch(int $batchId): float
    {
        $row = $this->selectSum('amount')->where('batch_id', $batchId)->first();
        return (float) ($row['amount'] ?? 0);
    }
}
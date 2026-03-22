<?php

namespace App\Models;

use CodeIgniter\Model;

class BillModel extends Model
{
    protected $table      = 'bills';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'batch_id',       // links to billing_batches
        'school_id',
        'scholar_id',     // individual scholar
        'billing_period', // period description
        'amount_due',     // amount for individual scholar
        'amount_paid',    // amount paid so far
        'due_date',
        'status',         // unpaid | partial | paid
        'remarks',
        'posted_by',
    ];

    protected $useTimestamps = true;

    // ----------------------------------------------------------------
    // Get the bill linked to a batch (admin-side)
    // ----------------------------------------------------------------
    public function getByBatch(int $batchId): array|null
    {
        return $this->where('batch_id', $batchId)->first();
    }

    // All bills for admin report
    public function getAllWithSchool(): array
    {
        return $this->select('bills.id, bills.batch_id, bills.school_id, bills.amount_due, bills.amount_paid, bills.due_date, bills.status, bills.remarks, bills.posted_by, bills.created_at, bills.updated_at, schools.name AS school_name')
            ->join('schools', 'schools.id = bills.school_id')
            ->orderBy('bills.created_at', 'DESC')
            ->findAll();
    }
}
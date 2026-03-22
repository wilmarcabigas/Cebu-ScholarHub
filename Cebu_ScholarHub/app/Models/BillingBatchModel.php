<?php

namespace App\Models;

use CodeIgniter\Model;

class BillingBatchModel extends Model
{
    protected $table      = 'billing_batches';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'school_id',
        'semester',
        'school_year',
        'batch_label',
        'total_amount',
        'status',
        'submitted_at',
        'submitted_by',
        'remarks',
        'rejection_remarks',
        'receipt_confirmed_at',
        'receipt_confirmed_by',
    ];

    protected $useTimestamps = true;

    // ----------------------------------------------------------------
    // Fetch batches for a specific school
    // ----------------------------------------------------------------
    public function getBatchesForSchool(int $schoolId): array
    {
        return $this->select('billing_batches.*, schools.name AS school_name')
            ->join('schools', 'schools.id = billing_batches.school_id')
            ->where('billing_batches.school_id', $schoolId)
            ->orderBy('billing_batches.created_at', 'DESC')
            ->findAll();
    }

    // Admin sees all batches across schools
    public function getAllBatchesForAdmin(): array
    {
        return $this->select('billing_batches.*, schools.name AS school_name')
            ->join('schools', 'schools.id = billing_batches.school_id')
            ->orderBy('billing_batches.created_at', 'DESC')
            ->findAll();
    }

    public function getBatchWithSchool(int $batchId): array|null
    {
        return $this->select('billing_batches.*, schools.name AS school_name')
            ->join('schools', 'schools.id = billing_batches.school_id')
            ->where('billing_batches.id', $batchId)
            ->first();
    }

    // ----------------------------------------------------------------
    // Auto-update batch status based on its bills' payment state
    // ----------------------------------------------------------------
    public function updateBatchStatus(int $batchId): void
    {
        $db    = db_connect();
        $bills = $db->table('bills')
            ->where('batch_id', $batchId)
            ->get()
            ->getResultArray();

        if (empty($bills)) {
            return;
        }

        $totalDue  = array_sum(array_column($bills, 'amount_due'));
        $totalPaid = array_sum(array_column($bills, 'amount_paid'));

        if ($totalPaid >= $totalDue) {
            $status = 'paid';
        } elseif ($totalPaid > 0) {
            $status = 'partial';
        } else {
            $status = 'received'; // bills exist but nothing paid yet
        }

        $this->update($batchId, ['status' => $status]);
    }

    // ----------------------------------------------------------------
    // Check if a duplicate batch exists for the same period
    // ----------------------------------------------------------------
    public function duplicateExists(int $schoolId, string $semester, string $schoolYear, int $excludeId = 0): bool
    {
        $builder = $this->where('school_id', $schoolId)
            ->where('semester', $semester)
            ->where('school_year', $schoolYear);

        if ($excludeId > 0) {
            $builder = $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }
}
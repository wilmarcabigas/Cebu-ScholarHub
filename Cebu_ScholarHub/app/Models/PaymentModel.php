<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table      = 'payments';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'bill_id',
        'amount_paid',
        'payment_date',
        'updated_by',
        'remarks',
        'voucher_no',
    ];

    protected $useTimestamps = true;

    /**
     * Get all payments for a specific bill
     */
    public function getPaymentsForBill(int $billId): array
    {
        return $this->select('payments.id, payments.bill_id, payments.amount_paid, payments.payment_date, payments.updated_by, payments.remarks, payments.created_at, payments.updated_at, users.full_name AS updated_by_name')
            ->join('users', 'users.id = payments.updated_by', 'left')
            ->where('payments.bill_id', $billId)
            ->orderBy('payments.payment_date', 'ASC')
            ->findAll();
    }

    /**
     * Get total amount paid for a bill
     */
    public function totalPaidForBill(int $billId): float
    {
        $row = $this->selectSum('amount_paid')->where('bill_id', $billId)->first();
        return (float) ($row['amount_paid'] ?? 0);
    }

    /**
     * Get all payments per school (for reports)
     */
    public function getPaymentsBySchool(int $schoolId): array
    {
        return $this->select(
                'payments.id,
                 payments.bill_id,
                 payments.amount_paid,
                 payments.payment_date,
                 payments.updated_by,
                 payments.remarks,
                 payments.created_at,
                 payments.updated_at,
                 bills.amount_due,
                 bills.amount_paid AS bill_amount_paid,
                 bills.status AS bill_status,
                 bills.due_date,
                 schools.name AS school_name'
            )
            ->join('bills', 'bills.id = payments.bill_id')
            ->join('schools', 'schools.id = bills.school_id')
            ->where('bills.school_id', $schoolId)
            ->orderBy('payments.payment_date', 'DESC')
            ->findAll();
    }

    /**
     * Get payment summary by school (total received, pending count)
     */
    public function getSummaryBySchool(): array
    {
        return $this->select(
                'schools.id,
                 schools.name,
                 COUNT(DISTINCT bills.id) AS total_bills,
                 SUM(bills.amount_due) AS total_due,
                 SUM(COALESCE(payments.amount_paid, 0)) AS total_paid'
            )
            ->join('bills', 'bills.id = payments.bill_id')
            ->join('schools', 'schools.id = bills.school_id')
            ->groupBy('schools.id, schools.name')
            ->findAll();
    }

    /**
     * Get received payments within date range
     */
    public function getPaymentsBetweenDates($startDate, $endDate): array
    {
        return $this->select(
                'payments.id,
                 payments.bill_id,
                 payments.amount_paid,
                 payments.payment_date,
                 payments.updated_by,
                 payments.remarks,
                 payments.created_at,
                 payments.updated_at,
                 bills.amount_due,
                 schools.name AS school_name,
                 users.full_name AS received_by'
            )
            ->join('bills', 'bills.id = payments.bill_id')
            ->join('schools', 'schools.id = bills.school_id')
            ->join('users', 'users.id = payments.updated_by', 'left')
            ->where('DATE(payments.payment_date) >=', $startDate)
            ->where('DATE(payments.payment_date) <=', $endDate)
            ->orderBy('payments.payment_date', 'DESC')
            ->findAll();
    }

    /**
     * Get unpaid bills (amount_paid < amount_due)
     */
    public function getUnpaidBills(): array
    {
        $db = db_connect();
        $builder = $db->table('bills')
            ->select('bills.id, bills.batch_id, bills.school_id, bills.amount_due, bills.amount_paid, bills.due_date, bills.status, bills.remarks, bills.posted_by, bills.created_at, bills.updated_at, schools.name AS school_name')
            ->join('schools', 'schools.id = bills.school_id')
            ->orderBy('bills.due_date', 'ASC');
        
        $results = $builder->get()->getResultArray();
        
        // Filter unpaid bills
        return array_filter($results, function($bill) {
            return $bill['amount_paid'] < $bill['amount_due'];
        });
    }

    /**
     * Get monthly payment summary
     */
    public function getMonthlyPaymentSummary(): array
    {
        $db = db_connect();
        $builder = $db->table('payments')
            ->select('MONTH(payment_date) AS month, YEAR(payment_date) AS year, SUM(amount_paid) AS total')
            ->groupBy('YEAR(payment_date), MONTH(payment_date)')
            ->orderBy('YEAR(payment_date)', 'DESC')
            ->orderBy('MONTH(payment_date)', 'DESC');
        
        return $builder->get()->getResultArray();
    }
}
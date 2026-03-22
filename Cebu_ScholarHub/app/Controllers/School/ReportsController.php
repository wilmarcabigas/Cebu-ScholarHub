<?php

namespace App\Controllers\School;

use App\Controllers\BaseController;
use App\Models\BillingBatchModel;
use App\Models\BillingItemModel;
use App\Models\BillModel;
use App\Models\PaymentModel;
use App\Models\ScholarModel;
use App\Models\SchoolModel;

class ReportsController extends BaseController
{
    // ------------------------------------------------------------------
    // INDEX — school admin views report options
    // ------------------------------------------------------------------
    public function index()
    {
        $schoolId = auth_school_id();
        
        // Get summary data
        $batchModel = new BillingBatchModel();
        $billModel = new BillModel();
        $scholarModel = new ScholarModel();

        $totalBillings = $batchModel->where('school_id', $schoolId)->countAllResults();
        $totalScholars = $scholarModel->where('school_id', $schoolId)->where('status', 'active')->countAllResults();
        
        $totalDue = 0;
        $totalPaid = 0;
        $bills = $billModel->where('school_id', $schoolId)->findAll();
        foreach ($bills as $bill) {
            $totalDue += $bill['amount_due'];
            $totalPaid += $bill['amount_paid'];
        }

        return view('school/reports/index', [
            'totalBillings' => $totalBillings,
            'totalScholars' => $totalScholars,
            'totalDue' => $totalDue,
            'totalPaid' => $totalPaid,
            'outstanding' => $totalDue - $totalPaid,
        ]);
    }

    // ------------------------------------------------------------------
    // PAYMENT_HISTORY — school sees payment history for all their bills
    // ------------------------------------------------------------------
    public function paymentHistory()
    {
        $schoolId = auth_school_id();
        $paymentModel = new PaymentModel();

        $payments = $paymentModel->getPaymentsBySchool($schoolId);

        return view('school/reports/payment_history', [
            'payments' => $payments,
        ]);
    }

    // ------------------------------------------------------------------
    // BILLING_SHEET — printable billing sheet for the school
    // ------------------------------------------------------------------
    public function billingSheet(int $batchId)
    {
        $schoolId = auth_school_id();
        $batchModel = new BillingBatchModel();
        $itemModel = new BillingItemModel();

        $batch = $batchModel->find($batchId);
        if (!$batch || $batch['school_id'] != $schoolId) {
            return redirect()->back()->with('error', 'Billing not found.');
        }

        $items = $itemModel->getItemsWithScholars($batchId);

        return view('school/reports/billing_sheet', [
            'batch' => $batch,
            'items' => $items,
            'printMode' => true,
        ]);
    }

    // ------------------------------------------------------------------
    // STATUS_SUMMARY — payment status summary table
    // ------------------------------------------------------------------
    public function statusSummary()
    {
        $schoolId = auth_school_id();
        $billModel = new BillModel();

        $bills = $billModel->select('bills.id, bills.batch_id, bills.school_id, bills.scholar_id, bills.billing_period, bills.amount_due, bills.amount_paid, bills.due_date, bills.status, bills.remarks, bills.posted_by, bills.created_at, bills.updated_at, (bills.amount_due - bills.amount_paid) AS balance')
            ->where('school_id', $schoolId)
            ->orderBy('bills.due_date', 'ASC')
            ->findAll();

        return view('school/reports/status_summary', [
            'bills' => $bills,
        ]);
    }

    // ------------------------------------------------------------------
    // PAYMENT_REPORT — generate payment report as CSV/PDF
    // ------------------------------------------------------------------
    public function exportPaymentHistory()
    {
        $schoolId = auth_school_id();
        $paymentModel = new PaymentModel();
        $schoolModel = new SchoolModel();

        $school = $schoolModel->find($schoolId);
        $payments = $paymentModel->getPaymentsBySchool($schoolId);

        $filename = 'payment_history_' . $school['name'] . '_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

        // Header row
        fputcsv($output, ['Payment Date', 'School', 'Amount', 'Bill Status', 'Remarks']);

        // Data rows
        foreach ($payments as $payment) {
            fputcsv($output, [
                date('M d, Y', strtotime($payment['payment_date'])),
                $payment['school_name'],
                '₱' . number_format($payment['amount_paid'], 2),
                ucfirst($payment['bill_status']),
                $payment['remarks'] ?? '',
            ]);
        }

        fclose($output);
        exit;
    }
}

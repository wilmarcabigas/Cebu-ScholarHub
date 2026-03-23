<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BillingBatchModel;
use App\Models\BillingItemModel;
use App\Models\BillModel;
use App\Models\PaymentModel;
use App\Models\SchoolModel;

class ReportsController extends BaseController
{
    // ------------------------------------------------------------------
    // INDEX — overall admin financial dashboard
    // ------------------------------------------------------------------
    public function index()
    {
        $billModel    = new BillModel();
        $paymentModel = new PaymentModel();
        $batchModel   = new BillingBatchModel();
        $schoolModel  = new SchoolModel();
        $db           = db_connect();

        // --- Overall financial summary ---
        $bills          = $billModel->getAllWithSchool();
        $totalBilled    = array_sum(array_column($bills, 'amount_due'));
        $totalPaid      = array_sum(array_column($bills, 'amount_paid'));
        $totalBalance   = $totalBilled - $totalPaid;

        // --- Per-school payment summary ---
        $schoolSummary = $paymentModel->getSummaryBySchool();

        // --- Per-scholar payment history ---
        $scholarPayments = $db->query("
            SELECT
                sch.id_num,
                CONCAT(sch.last_name, ', ', sch.first_name, ' ', IFNULL(sch.middle_name,'')) AS scholar_name,
                sch.course,
                sch.year_level,
                sc.name AS school_name,
                bb.semester,
                bb.school_year,
                bi.amount AS billed_amount,
                bb.status AS batch_status
            FROM billing_items bi
            JOIN scholars sch ON sch.id = bi.scholar_id
            JOIN schools sc ON sc.id = sch.school_id
            JOIN billing_batches bb ON bb.id = bi.batch_id
            ORDER BY sch.last_name ASC, bb.school_year DESC
        ")->getResultArray();

        return view('admin/reports/index', [
            'bills'           => $bills,
            'totalBilled'     => $totalBilled,
            'totalPaid'       => $totalPaid,
            'totalBalance'    => $totalBalance,
            'schoolSummary'   => $schoolSummary,
            'scholarPayments' => $scholarPayments,
        ]);
    }

    // ------------------------------------------------------------------
    // PAYMENT_STATUS — detailed payment status by school
    // ------------------------------------------------------------------
    public function paymentStatus()
    {
        $billModel = new BillModel();
        $db = db_connect();

        $bills = $billModel->select('bills.id, bills.batch_id, bills.school_id, bills.amount_due, bills.amount_paid, bills.due_date, bills.status, bills.remarks, bills.posted_by, bills.created_at, bills.updated_at, schools.name AS school_name, (bills.amount_due - bills.amount_paid) AS balance')
            ->join('schools', 'schools.id = bills.school_id')
            ->orderBy('bills.due_date', 'ASC')
            ->findAll();

        // Group by status
        $byStatus = [];
        foreach ($bills as $bill) {
            $status = $bill['status'];
            if (!isset($byStatus[$status])) {
                $byStatus[$status] = [];
            }
            $byStatus[$status][] = $bill;
        }

        return view('admin/reports/payment_status', [
            'bills' => $bills,
            'byStatus' => $byStatus,
        ]);
    }

    // ------------------------------------------------------------------
    // FINANCIAL_REPORT — comprehensive financial report
    // ------------------------------------------------------------------
    public function financialReport()
    {
        $paymentModel = new PaymentModel();
        $billModel = new BillModel();
        $db = db_connect();

        // Get all payments
        $payments = $paymentModel->select(
                'payments.id, payments.bill_id, payments.amount_paid, payments.payment_date, payments.updated_by, payments.remarks, payments.created_at, payments.updated_at,
                 bills.amount_due,
                 bills.status,
                 schools.name AS school_name'
            )
            ->join('bills', 'bills.id = payments.bill_id')
            ->join('schools', 'schools.id = bills.school_id')
            ->orderBy('payments.payment_date', 'DESC')
            ->findAll();

        // Monthly summary
        $monthlySummary = $paymentModel->getMonthlyPaymentSummary();

        // Outstanding bills
        $unpaid = $paymentModel->getUnpaidBills();

        return view('admin/reports/financial_report', [
            'payments' => $payments,
            'monthlySummary' => $monthlySummary,
            'unpaid' => $unpaid,
        ]);
    }

    // ------------------------------------------------------------------
    // BILLING_SHEET_REPORT — all billing sheets for printing
    // ------------------------------------------------------------------
    public function billingSheets()
    {
        $batchModel = new BillingBatchModel();
        $itemModel = new BillingItemModel();

        // Get all submitted/received batches
        $batches = $batchModel->select('billing_batches.id, billing_batches.school_id, billing_batches.batch_label, billing_batches.semester, billing_batches.school_year, billing_batches.status, billing_batches.created_at, billing_batches.updated_at, billing_batches.submitted_at, schools.name AS school_name')
            ->join('schools', 'schools.id = billing_batches.school_id')
            ->whereIn('billing_batches.status', ['submitted', 'received', 'paid'])
            ->orderBy('billing_batches.created_at', 'DESC')
            ->findAll();

        $batchesWithItems = [];
        foreach ($batches as $batch) {
            $items = $itemModel->getItemsWithScholars($batch['id']);
            $batchesWithItems[] = [
                'batch' => $batch,
                'items' => $items,
            ];
        }

        return view('admin/reports/billing_sheets', [
            'batchesWithItems' => $batchesWithItems,
        ]);
    }

    // ------------------------------------------------------------------
    // SCHOLAR_PAYMENT_HISTORY — per-scholar payment history
    // ------------------------------------------------------------------
    public function scholarPaymentHistory()
    {
        $db = db_connect();

        $payments = $db->query("
            SELECT
                sch.id_num,
                sch.voucher_no,
                CONCAT(sch.last_name, ', ', sch.first_name, ' ', IFNULL(sch.middle_name,'')) AS scholar_name,
                sch.course,
                sch.year_level,
                sc.name AS school_name,
                bb.semester,
                bb.school_year,
                bi.amount AS billed,
                COALESCE(SUM(p.amount_paid), 0) AS paid,
                (bi.amount - COALESCE(SUM(p.amount_paid), 0)) AS balance,
                MAX(p.payment_date) AS last_payment_date
            FROM billing_items bi
            JOIN scholars sch ON sch.id = bi.scholar_id
            JOIN schools sc ON sc.id = sch.school_id
            JOIN billing_batches bb ON bb.id = bi.batch_id
            LEFT JOIN bills b ON b.batch_id = bb.id
            LEFT JOIN payments p ON p.bill_id = b.id
            GROUP BY bi.id, sch.id, bb.id
            ORDER BY sch.last_name ASC, bb.school_year DESC
        ")->getResultArray();

        return view('admin/reports/scholar_payment_history', [
            'payments' => $payments,
        ]);
    }

    // ------------------------------------------------------------------
    // EXPORT_FINANCIAL_REPORT — export financial report as CSV
    // ------------------------------------------------------------------
    public function exportFinancialReport()
    {
        $paymentModel = new PaymentModel();

        $payments = $paymentModel->getPaymentsBetweenDates(
            $this->request->getGet('start_date') ?? date('Y-01-01'),
            $this->request->getGet('end_date') ?? date('Y-m-d')
        );

        $filename = 'financial_report_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

        // Header row
        fputcsv($output, ['Payment Date', 'School', 'Amount Due', 'Amount Paid', 'Bill Status', 'Received By']);

        // Data rows
        foreach ($payments as $payment) {
            fputcsv($output, [
                date('M d, Y', strtotime($payment['payment_date'])),
                $payment['school_name'],
                '₱' . number_format($payment['amount_due'], 2),
                '₱' . number_format($payment['amount_paid'], 2),
                ucfirst($payment['bill_status']),
                $payment['received_by'] ?? 'System',
            ]);
        }

        fclose($output);
        exit;
    }

    // ------------------------------------------------------------------
    // EXPORT_SCHOLAR_REPORT — export scholar payment history as CSV
    // ------------------------------------------------------------------
    public function exportScholarReport()
    {
        $db = db_connect();

        $payments = $db->query("
            SELECT
                sch.id_num,
                sch.voucher_no,
                CONCAT(sch.last_name, ', ', sch.first_name, ' ', IFNULL(sch.middle_name,'')) AS scholar_name,
                sch.course,
                sch.year_level,
                sc.name AS school_name,
                bb.semester,
                bb.school_year,
                bi.amount AS billed,
                COALESCE(SUM(p.amount_paid), 0) AS paid,
                (bi.amount - COALESCE(SUM(p.amount_paid), 0)) AS balance
            FROM billing_items bi
            JOIN scholars sch ON sch.id = bi.scholar_id
            JOIN schools sc ON sc.id = sch.school_id
            JOIN billing_batches bb ON bb.id = bi.batch_id
            LEFT JOIN bills b ON b.batch_id = bb.id
            LEFT JOIN payments p ON p.bill_id = b.id
            GROUP BY bi.id, sch.id, bb.id
            ORDER BY sch.last_name ASC, bb.school_year DESC
        ")->getResultArray();

        $filename = 'scholar_payment_report_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

        // Header row
        fputcsv($output, ['ID Num', 'Voucher No.', 'Scholar Name', 'Course', 'Year', 'School', 
                         'Semester', 'School Year', 'Billed', 'Paid', 'Balance']);

        // Data rows
        foreach ($payments as $payment) {
            fputcsv($output, [
                $payment['id_num'] ?? '',
                $payment['voucher_no'] ?? '',
                $payment['scholar_name'],
                $payment['course'],
                $payment['year_level'],
                $payment['school_name'],
                $payment['semester'],
                $payment['school_year'],
                '₱' . number_format($payment['billed'], 2),
                '₱' . number_format($payment['paid'], 2),
                '₱' . number_format($payment['balance'], 2),
            ]);
        }

        fclose($output);
        exit;
    }
}
<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BillingBatchModel;
use App\Models\BillModel;
use App\Models\PaymentModel;
use App\Models\SchoolModel;

class ReportsController extends BaseController
{
    protected BillingBatchModel $batchModel;
    protected BillModel $billModel;
    protected PaymentModel $paymentModel;
    protected SchoolModel $schoolModel;

    public function __construct()
    {
        $this->batchModel   = new BillingBatchModel();
        $this->billModel    = new BillModel();
        $this->paymentModel = new PaymentModel();
        $this->schoolModel  = new SchoolModel();
    }

    // ------------------------------------------------------------------
    // INDEX — Admin reports dashboard
    // ------------------------------------------------------------------
    public function index()
    {
        $overview = $this->getOverviewTotals();

        return view('admin/reports/dashboard', [
            'totalBilled'           => $overview['totalBilled'],
            'totalPaid'             => $overview['totalPaid'],
            'totalBalance'          => $overview['totalBalance'],
            'schoolSummary'         => $this->getSchoolSummary(),
            'recentScholarPayments' => array_slice($this->getScholarPayments(), 0, 8),
            'recentBills'           => array_slice($this->billModel->getAllWithSchool(), 0, 8),
        ]);
    }

    // ------------------------------------------------------------------
    // PAYMENT STATUS — Payment status by school
    // ------------------------------------------------------------------
    public function paymentStatus()
    {
        return view('admin/reports/payment-status', [
            'schools' => $this->getSchoolSummary(),
        ]);
    }

    // ------------------------------------------------------------------
    // FINANCIAL REPORT — Overall financial summary
    // ------------------------------------------------------------------
    public function financialReport()
    {
        $overview = $this->getOverviewTotals();
        $statusAmounts = $this->getBatchStatusAmounts();
        $billCounts = $this->getBillStatusCounts();

        $schoolData = array_map(static function (array $row): array {
            return [
                'school_name' => $row['school_name'],
                'total_due'   => $row['amount_due'],
                'collected'   => $row['amount_paid'],
            ];
        }, $this->getSchoolSummary());

        return view('admin/reports/financial-report', [
            'totalAmount'        => $overview['totalBilled'],
            'totalCollected'     => $overview['totalPaid'],
            'draftAmount'        => $statusAmounts['draft'],
            'submittedAmount'    => $statusAmounts['submitted'],
            'receivedAmount'     => $statusAmounts['received'],
            'paidAmount'         => $statusAmounts['paid'],
            'pendingBills'       => $billCounts['pending'],
            'partiallyPaidBills' => $billCounts['partial'],
            'fullyPaidBills'     => $billCounts['paid'],
            'schoolData'         => $schoolData,
        ]);
    }

    // ------------------------------------------------------------------
    // BILLING SHEETS — Printable billing sheets
    // ------------------------------------------------------------------
    public function billingSheets()
    {
        $schoolId = (int) ($this->request->getGet('school_id') ?? 0);
        $status   = (string) ($this->request->getGet('status') ?? '');

        $query = $this->batchModel
            ->select('billing_batches.id, billing_batches.school_id, billing_batches.batch_label, billing_batches.semester, billing_batches.school_year, billing_batches.total_amount, billing_batches.status, billing_batches.created_at, schools.name AS school_name')
            ->join('schools', 'schools.id = billing_batches.school_id')
            ->orderBy('billing_batches.created_at', 'DESC');

        if ($schoolId > 0) {
            $query->where('billing_batches.school_id', $schoolId);
        }

        if (in_array($status, ['draft', 'submitted', 'received', 'partial', 'paid'], true)) {
            $query->where('billing_batches.status', $status);
        }

        $billingSheets = [];

        foreach ($query->findAll() as $batch) {
            $items = $this->getBillingSheetItems((int) $batch['id']);

            $billingSheets[] = $batch + [
                'items'        => $items,
                'items_count'  => count($items),
                'amount_paid'  => array_sum(array_column($items, 'amount_paid')),
                'total_amount' => array_sum(array_column($items, 'amount')),
            ];
        }

        return view('admin/reports/billing-sheets-admin', [
            'schools'       => $this->schoolModel->orderBy('name', 'ASC')->findAll(),
            'billingSheets' => $billingSheets,
        ]);
    }

    // ------------------------------------------------------------------
    // SCHOLAR PAYMENT HISTORY — Per-scholar payment history
    // ------------------------------------------------------------------
    public function scholarPaymentHistory()
    {
        $schoolId      = (int) ($this->request->getGet('school_id') ?? 0);
        $paymentStatus = (string) ($this->request->getGet('payment_status') ?? '');

        $scholarPayments = $this->getScholarPayments($schoolId, $paymentStatus);
        $fullyPaidCount = count(array_filter(
            $scholarPayments,
            static fn (array $row): bool => $row['payment_status'] === 'paid'
        ));

        return view('admin/reports/scholar-payment-history-admin', [
            'schools'         => $this->schoolModel->orderBy('name', 'ASC')->findAll(),
            'scholarPayments' => $scholarPayments,
            'totalScholars'   => count($scholarPayments),
            'fullyPaidCount'  => $fullyPaidCount,
        ]);
    }

    // ------------------------------------------------------------------
    // EXPORT FINANCIAL REPORT — CSV export of financial data
    // ------------------------------------------------------------------
    public function exportFinancialReport()
    {
        $rows = array_map(static function (array $bill): array {
            $amountDue = (float) ($bill['amount_due'] ?? 0);
            $amountPaid = (float) ($bill['amount_paid'] ?? 0);

            return [
                $bill['school_name'] ?? 'N/A',
                $bill['batch_id'] ?? '',
                $bill['scholar_id'] ?? '',
                $bill['due_date'] ? date('Y-m-d', strtotime($bill['due_date'])) : '',
                number_format($amountDue, 2, '.', ''),
                number_format($amountPaid, 2, '.', ''),
                number_format($amountDue - $amountPaid, 2, '.', ''),
                strtoupper((string) ($bill['status'] ?? 'unpaid')),
            ];
        }, $this->billModel->getAllWithSchool());

        return $this->streamCsv(
            'financial_report_' . date('Y-m-d') . '.csv',
            ['School', 'Batch ID', 'Scholar ID', 'Due Date', 'Amount Due', 'Amount Paid', 'Balance', 'Status'],
            $rows
        );
    }

    // ------------------------------------------------------------------
    // EXPORT SCHOLAR REPORT — CSV export of scholar payment data
    // ------------------------------------------------------------------
    public function exportScholarReport()
    {
        $rows = array_map(static function (array $payment): array {
            return [
                $payment['id_num'] ?? '',
                $payment['scholar_name'] ?? 'N/A',
                $payment['school_name'] ?? 'N/A',
                $payment['course'] ?? '',
                $payment['year_level'] ?? '',
                $payment['semester'] ?? '',
                $payment['school_year'] ?? '',
                number_format((float) ($payment['amount_due'] ?? 0), 2, '.', ''),
                number_format((float) ($payment['amount_paid'] ?? 0), 2, '.', ''),
                number_format((float) ($payment['balance'] ?? 0), 2, '.', ''),
                strtoupper((string) ($payment['payment_status'] ?? 'unpaid')),
            ];
        }, $this->getScholarPayments());

        return $this->streamCsv(
            'scholar_report_' . date('Y-m-d') . '.csv',
            ['ID Number', 'Scholar Name', 'School', 'Course', 'Year Level', 'Semester', 'School Year', 'Amount Due', 'Amount Paid', 'Balance', 'Payment Status'],
            $rows
        );
    }

    private function getOverviewTotals(): array
    {
        $row = db_connect()->table('bills')
            ->select('COALESCE(SUM(amount_due), 0) AS total_billed, COALESCE(SUM(amount_paid), 0) AS total_paid')
            ->get()
            ->getRowArray();

        $totalBilled = (float) ($row['total_billed'] ?? 0);
        $totalPaid = (float) ($row['total_paid'] ?? 0);

        return [
            'totalBilled'  => $totalBilled,
            'totalPaid'    => $totalPaid,
            'totalBalance' => $totalBilled - $totalPaid,
        ];
    }

    private function getSchoolSummary(): array
    {
        $rows = db_connect()->query(
            "
            SELECT
                s.id,
                s.name AS school_name,
                COUNT(DISTINCT b.id) AS bills_count,
                COALESCE(SUM(b.amount_due), 0) AS amount_due,
                COALESCE(SUM(b.amount_paid), 0) AS amount_paid
            FROM schools s
            LEFT JOIN bills b ON b.school_id = s.id
            GROUP BY s.id, s.name
            ORDER BY s.name ASC
            "
        )->getResultArray();

        return array_map(static function (array $row): array {
            $amountDue = (float) ($row['amount_due'] ?? 0);
            $amountPaid = (float) ($row['amount_paid'] ?? 0);
            $balance = $amountDue - $amountPaid;

            return [
                'id'              => (int) ($row['id'] ?? 0),
                'name'            => $row['school_name'] ?? 'N/A',
                'school_name'     => $row['school_name'] ?? 'N/A',
                'total_bills'     => (int) ($row['bills_count'] ?? 0),
                'bills_count'     => (int) ($row['bills_count'] ?? 0),
                'total_due'       => $amountDue,
                'amount_due'      => $amountDue,
                'total_paid'      => $amountPaid,
                'amount_paid'     => $amountPaid,
                'balance'         => $balance,
                'collection_rate' => $amountDue > 0 ? round(($amountPaid / $amountDue) * 100, 1) : 0,
            ];
        }, $rows);
    }

    private function getBatchStatusAmounts(): array
    {
        $rows = db_connect()->table('billing_batches')
            ->select('status, COALESCE(SUM(total_amount), 0) AS total_amount')
            ->groupBy('status')
            ->get()
            ->getResultArray();

        $totals = [
            'draft'     => 0.0,
            'submitted' => 0.0,
            'received'  => 0.0,
            'paid'      => 0.0,
        ];

        foreach ($rows as $row) {
            $status = (string) ($row['status'] ?? '');

            if (array_key_exists($status, $totals)) {
                $totals[$status] = (float) ($row['total_amount'] ?? 0);
            }
        }

        return $totals;
    }

    private function getBillStatusCounts(): array
    {
        $rows = db_connect()->table('bills')
            ->select('status, COUNT(*) AS total_count')
            ->groupBy('status')
            ->get()
            ->getResultArray();

        $counts = [
            'pending' => 0,
            'partial' => 0,
            'paid'    => 0,
        ];

        foreach ($rows as $row) {
            $status = (string) ($row['status'] ?? '');
            $count = (int) ($row['total_count'] ?? 0);

            if ($status === 'paid') {
                $counts['paid'] += $count;
                continue;
            }

            if ($status === 'partial') {
                $counts['partial'] += $count;
                continue;
            }

            $counts['pending'] += $count;
        }

        return $counts;
    }

    private function getBillingSheetItems(int $batchId): array
    {
        $rows = db_connect()->query(
            "
            SELECT
                bi.id,
                bi.batch_id,
                bi.scholar_id,
                bi.amount,
                COALESCE(b.amount_paid, 0) AS amount_paid,
                CONCAT(
                    sch.last_name,
                    ', ',
                    sch.first_name,
                    CASE
                        WHEN sch.middle_name IS NOT NULL AND sch.middle_name <> '' THEN CONCAT(' ', sch.middle_name)
                        ELSE ''
                    END
                ) AS scholar_name,
                sch.course,
                sch.year_level
            FROM billing_items bi
            JOIN scholars sch ON sch.id = bi.scholar_id
            LEFT JOIN bills b
                ON b.batch_id = bi.batch_id
               AND b.scholar_id = bi.scholar_id
            WHERE bi.batch_id = ?
            ORDER BY sch.last_name ASC, sch.first_name ASC
            ",
            [$batchId]
        )->getResultArray();

        return array_map(static function (array $row): array {
            $row['amount'] = (float) ($row['amount'] ?? 0);
            $row['amount_paid'] = (float) ($row['amount_paid'] ?? 0);

            return $row;
        }, $rows);
    }

    private function getScholarPayments(int $schoolId = 0, string $paymentStatus = ''): array
    {
        $params = [];
        $where = '';

        if ($schoolId > 0) {
            $where = 'WHERE sc.id = ?';
            $params[] = $schoolId;
        }

        $rows = db_connect()->query(
            "
            SELECT
                bi.id AS billing_item_id,
                bi.batch_id,
                COALESCE(sch.id_num, '') AS id_num,
                CONCAT(
                    sch.last_name,
                    ', ',
                    sch.first_name,
                    CASE
                        WHEN sch.middle_name IS NOT NULL AND sch.middle_name <> '' THEN CONCAT(' ', sch.middle_name)
                        ELSE ''
                    END
                ) AS scholar_name,
                sch.course,
                sch.year_level,
                sc.name AS school_name,
                bb.semester,
                bb.school_year,
                bb.status AS batch_status,
                COALESCE(bi.amount, 0) AS amount_due,
                COALESCE(b.amount_paid, 0) AS amount_paid,
                COALESCE(bi.amount, 0) - COALESCE(b.amount_paid, 0) AS balance,
                MAX(p.payment_date) AS last_payment_date
            FROM billing_items bi
            JOIN scholars sch ON sch.id = bi.scholar_id
            JOIN schools sc ON sc.id = sch.school_id
            JOIN billing_batches bb ON bb.id = bi.batch_id
            LEFT JOIN bills b
                ON b.batch_id = bi.batch_id
               AND b.scholar_id = bi.scholar_id
            LEFT JOIN payments p ON p.bill_id = b.id
            {$where}
            GROUP BY
                bi.id,
                bi.batch_id,
                sch.id_num,
                sch.last_name,
                sch.first_name,
                sch.middle_name,
                sch.course,
                sch.year_level,
                sc.name,
                bb.semester,
                bb.school_year,
                bb.status,
                bi.amount,
                b.amount_paid
            ORDER BY sch.last_name ASC, sch.first_name ASC, bb.school_year DESC
            ",
            $params
        )->getResultArray();

        $rows = array_map(function (array $row): array {
            $row['amount_due'] = (float) ($row['amount_due'] ?? 0);
            $row['amount_paid'] = (float) ($row['amount_paid'] ?? 0);
            $row['balance'] = (float) ($row['balance'] ?? 0);
            $row['payment_status'] = $this->resolvePaymentStatus($row['amount_due'], $row['amount_paid']);

            return $row;
        }, $rows);

        if (!in_array($paymentStatus, ['paid', 'partial', 'unpaid'], true)) {
            return $rows;
        }

        return array_values(array_filter(
            $rows,
            static fn (array $row): bool => $row['payment_status'] === $paymentStatus
        ));
    }

    private function resolvePaymentStatus(float $amountDue, float $amountPaid): string
    {
        if ($amountDue > 0 && $amountPaid >= $amountDue) {
            return 'paid';
        }

        if ($amountPaid > 0) {
            return 'partial';
        }

        return 'unpaid';
    }

    private function streamCsv(string $filename, array $headers, array $rows)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($output, $headers);

        foreach ($rows as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }
}

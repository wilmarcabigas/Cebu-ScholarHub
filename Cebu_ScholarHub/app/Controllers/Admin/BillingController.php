<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BillingBatchModel;
use App\Models\BillingItemModel;
use App\Models\BillModel;
use App\Models\PaymentModel;

class BillingController extends BaseController
{
    // ------------------------------------------------------------------
    // INDEX — office admin sees all billing batches from all schools
    // ------------------------------------------------------------------
    public function index()
    {
        $batchModel = new BillingBatchModel();
        $batches    = $batchModel->getAllBatchesForAdmin();

        return view('admin/billing/index', [
            'batches' => $batches,
        ]);
    }

    // ------------------------------------------------------------------
    // VIEW — admin views a single batch with items, bills & payments
    // ------------------------------------------------------------------
    public function view(int $batchId)
    {
        $batchModel   = new BillingBatchModel();
        $itemModel    = new BillingItemModel();
        $billModel    = new BillModel();
        $paymentModel = new PaymentModel();

        $batch = $batchModel->getBatchWithSchool($batchId);
        if (!$batch) {
            return redirect()->to(site_url('admin/billing'))
                ->with('error', 'Billing not found.');
        }

        $items    = $itemModel->getItemsWithScholars($batchId);
        $bills    = $billModel->where('batch_id', $batchId)->findAll();
        $payments = [];
        foreach ($bills as $bill) {
            $billPayments = $paymentModel->getPaymentsForBill($bill['id']);
            $payments     = array_merge($payments, $billPayments);
        }

        $totalAmountDue  = array_sum(array_column($bills, 'amount_due'));
        $totalAmountPaid = array_sum(array_column($bills, 'amount_paid'));

        return view('admin/billing/view', [
            'batch'           => $batch,
            'items'           => $items,
            'bills'           => $bills,
            'payments'        => $payments,
            'totalAmountDue'  => $totalAmountDue,
            'totalAmountPaid' => $totalAmountPaid,
        ]);
    }

    // ------------------------------------------------------------------
    // RECEIVE — admin posts the submitted batch as official bills
    // ------------------------------------------------------------------
    public function receive(int $batchId)
    {
        $batchModel = new BillingBatchModel();
        $billModel  = new BillModel();
        $itemModel  = new BillingItemModel();

        $batch = $batchModel->find($batchId);
        if (!$batch || $batch['status'] !== 'submitted') {
            return redirect()->back()
                ->with('error', 'Only submitted billings can be received.');
        }

        $dueDate = $this->request->getPost('due_date');
        if (!$dueDate) {
            return redirect()->back()->with('error', 'Due date is required.');
        }

        $db = db_connect();
        $db->transStart();

        $items = $itemModel->where('batch_id', $batchId)->findAll();

        foreach ($items as $item) {
            $billModel->insert([
                'batch_id'       => $batchId,
                'school_id'      => $batch['school_id'],
                'scholar_id'     => $item['scholar_id'],
                'billing_period' => $batch['semester'] . ' ' . $batch['school_year'],
                'amount_due'     => $item['amount'],
                'amount_paid'    => 0,
                'due_date'       => $dueDate,
                'status'         => 'unpaid',
                'remarks'        => '',
                'posted_by'      => auth_user()['id'],
            ]);
        }

        $batchModel->update($batchId, ['status' => 'received']);

        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->back()
                ->with('error', 'Failed to post bill. Please try again.');
        }

        log_audit(
            auth_id(),
            'RECEIVE',
            'billing_batches',
            $batchId,
            'Batch received and ' . count($items) . ' bills posted.'
        );

        return redirect()->back()
            ->with('success', 'Billing received and official bills posted for ' . count($items) . ' scholars.');
    }

    // ------------------------------------------------------------------
    // RECORD PAYMENT — admin records a bulk payment for a batch
    // Distributes the entered amount across unpaid bills in order
    // ------------------------------------------------------------------
    public function recordPayment(int $batchId)
    {
        $batchModel   = new BillingBatchModel();
        $billModel    = new BillModel();
        $paymentModel = new PaymentModel();

        $batch = $batchModel->find($batchId);
        if (!$batch || !in_array($batch['status'], ['received', 'partial'])) {
            return redirect()->back()
                ->with('error', 'Payment can only be recorded for received or partially paid billings.');
        }

        $amountPaid  = (float) $this->request->getPost('amount_paid');
        $paymentDate = $this->request->getPost('payment_date');
        $voucherNo   = trim($this->request->getPost('voucher_no') ?? '');
        $remarks     = trim($this->request->getPost('remarks') ?? '');

        if ($amountPaid <= 0) {
            return redirect()->back()->with('error', 'Amount must be greater than zero.');
        }
        if (empty($paymentDate)) {
            return redirect()->back()->with('error', 'Payment date is required.');
        }

        // Get all bills for this batch that still have remaining balance, ordered by id
        $bills = $billModel->where('batch_id', $batchId)
            ->where('status !=', 'paid')
            ->orderBy('id', 'ASC')
            ->findAll();

        if (empty($bills)) {
            return redirect()->back()->with('error', 'All bills in this batch are already fully paid.');
        }

        // Calculate total remaining to prevent overpayment
        $totalRemaining = array_sum(array_map(
            fn($b) => $b['amount_due'] - $b['amount_paid'],
            $bills
        ));

        if ($amountPaid > $totalRemaining) {
            return redirect()->back()->with('error',
                'Payment of ₱' . number_format($amountPaid, 2) .
                ' exceeds the remaining balance of ₱' . number_format($totalRemaining, 2) . '.'
            );
        }

        $db = db_connect();
        $db->transStart();

        $leftover = $amountPaid;

        foreach ($bills as $bill) {
            if ($leftover <= 0) break;

            $remaining = $bill['amount_due'] - $bill['amount_paid'];
            if ($remaining <= 0) continue;

            $pay = min($leftover, $remaining);

            $paymentModel->insert([
                'bill_id'      => $bill['id'],
                'amount_paid'  => $pay,
                'payment_date' => $paymentDate,
                'updated_by'   => auth_id(),
                'remarks'      => $remarks,
                'voucher_no'   => $voucherNo,
            ]);

            $newPaid   = $bill['amount_paid'] + $pay;
            $newStatus = $newPaid >= $bill['amount_due'] ? 'paid' : 'partial';

            $billModel->update($bill['id'], [
                'amount_paid' => $newPaid,
                'status'      => $newStatus,
            ]);

            $leftover -= $pay;
        }

        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->back()->with('error', 'Failed to record payment. Please try again.');
        }

        // Auto-update batch status after payment
        $batchModel->updateBatchStatus($batchId);

        log_audit(
            auth_id(),
            'RECORD_PAYMENT',
            'billing_batches',
            $batchId,
            'Payment of ₱' . number_format($amountPaid, 2) . ' recorded. Voucher: ' . ($voucherNo ?: 'N/A')
        );

        return redirect()->back()
            ->with('success', 'Payment of ₱' . number_format($amountPaid, 2) . ' recorded successfully.');
    }

    // ------------------------------------------------------------------
    // REJECT — admin rejects a submitted billing (requires reason)
    // ------------------------------------------------------------------
    public function reject(int $batchId)
    {
        $batchModel = new BillingBatchModel();

        $batch = $batchModel->find($batchId);
        if (!$batch || $batch['status'] !== 'submitted') {
            return redirect()->back()
                ->with('error', 'Only submitted billings can be rejected.');
        }

        $rejectionRemarks = trim($this->request->getPost('rejection_remarks') ?? '');

        if (strlen($rejectionRemarks) < 10) {
            return redirect()->back()
                ->with('error', 'Rejection reason is required (minimum 10 characters).');
        }

        $batchModel->update($batchId, [
            'status'            => 'draft',
            'rejection_remarks' => $rejectionRemarks,
        ]);

        log_audit(
            auth_id(),
            'REJECT',
            'billing_batches',
            $batchId,
            'Batch rejected: ' . $rejectionRemarks
        );

        return redirect()->back()
            ->with('success', 'Billing rejected. The school can edit and resubmit.');
    }

    // ------------------------------------------------------------------
    // PRINT — printable billing sheet
    // ------------------------------------------------------------------
    public function print(int $batchId)
    {
        $batchModel = new BillingBatchModel();
        $itemModel  = new BillingItemModel();

        $batch = $batchModel->getBatchWithSchool($batchId);
        if (!$batch) {
            return redirect()->to(site_url('admin/billing'))
                ->with('error', 'Billing not found.');
        }

        $items = $itemModel->getItemsWithScholars($batchId);

        return view('admin/billing/print', [
            'batch' => $batch,
            'items' => $items,
        ]);
    }
}

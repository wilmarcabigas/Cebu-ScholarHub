<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PaymentModel;
use App\Models\BillModel;
use App\Models\BillingBatchModel;

class PaymentController extends BaseController
{
    // ------------------------------------------------------------------
    // STORE — admin records a payment against a school bill
    // ------------------------------------------------------------------
    public function store()
    {
        $paymentModel = new PaymentModel();
        $billModel    = new BillModel();
        $batchModel   = new BillingBatchModel();

        $billId      = (int) $this->request->getPost('bill_id');
        $amountPaid  = (float) $this->request->getPost('amount_paid');
        $paymentDate = $this->request->getPost('payment_date');
        $remarks     = $this->request->getPost('remarks');

        // Step 1 — verify bill exists
        $bill = $billModel->find($billId);
        if (!$bill) {
            return redirect()->back()->with('error', 'Bill not found.');
        }

        // Step 2 — prevent overpayment
        $alreadyPaid = $paymentModel->totalPaidForBill($billId);
        $remaining   = $bill['amount_due'] - $alreadyPaid;

        if ($amountPaid > $remaining) {
            return redirect()->back()->with('error', 'Payment of ₱' . number_format($amountPaid, 2) . ' exceeds the remaining balance of ₱' . number_format($remaining, 2) . '.');
        }

        $db = db_connect();
        $db->transStart();

        // Step 3 — record the payment
        $paymentModel->insert([
            'bill_id'      => $billId,
            'amount_paid'  => $amountPaid,
            'payment_date' => $paymentDate,
            'remarks'      => $remarks,
            'updated_by'   => auth_user()['id'],
        ]);

        // Step 4 — update bill status
        $newTotalPaid = $alreadyPaid + $amountPaid;
        $newStatus    = $newTotalPaid >= $bill['amount_due'] ? 'paid' : 'partial';

        $billModel->update($billId, [
            'amount_paid' => $newTotalPaid,
            'status'      => $newStatus,
        ]);

        // Step 5 — sync batch status as well
        $batchModel->update($bill['batch_id'], [
            'status' => $newStatus,
        ]);

        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->back()->with('error', 'Payment failed. Please try again.');
        }

        return redirect()->back()->with('success', 'Payment of ₱' . number_format($amountPaid, 2) . ' recorded successfully.');
    }
}
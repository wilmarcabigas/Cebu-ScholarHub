<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\ActivityLogger;
use App\Models\BillModel;
use App\Models\PaymentModel;

class PaymentController extends BaseController
{
    public function store()
    {
        $paymentModel = new PaymentModel();
        $billModel = new BillModel();
        $activityLogger = new ActivityLogger();

        $billId = (int) $this->request->getPost('bill_id');
        $amountPaid = (float) $this->request->getPost('amount_paid');
        $paymentDate = $this->request->getPost('payment_date');
        $remarks = $this->request->getPost('remarks');
        $authUser = auth_user();
        $schoolId = $authUser['school_id'];

        $bill = $billModel
            ->select('bills.*')
            ->join('scholars', 'scholars.id = bills.scholar_id')
            ->where('bills.id', $billId)
            ->where('scholars.school_id', $schoolId)
            ->first();

        if (! $bill) {
            return redirect()->back()->with('error', 'Invalid bill or unauthorized access');
        }

        if ($amountPaid > $bill['amount_due']) {
            return redirect()->back()->with('error', 'Payment exceeds remaining balance');
        }

        $previousBillState = [
            'amount_due' => $bill['amount_due'],
            'status' => $bill['status'],
        ];

        $paymentModel->insert([
            'bill_id' => $billId,
            'amount_paid' => $amountPaid,
            'payment_date' => $paymentDate,
            'remarks' => $remarks,
            'updated_by' => $authUser['id'],
        ]);

        $paymentId = (int) $paymentModel->getInsertID();

        $newBalance = $bill['amount_due'] - $amountPaid;
        $newStatus = $newBalance <= 0 ? 'paid' : 'partial';

        $billModel->update($billId, [
            'amount_due' => max(0, $newBalance),
            'status' => $newStatus,
        ]);

        $activityLogger->logSchoolAccountAction(
            $authUser,
            'payment_recorded',
            'Payment recorded',
            "{$authUser['full_name']} recorded a payment for bill #{$billId}.",
            [
                'action' => 'create',
                'school_id' => (int) $schoolId,
                'subject_type' => 'payment',
                'subject_id' => $paymentId,
                'old_values' => [
                    'bill' => $previousBillState,
                ],
                'new_values' => [
                    'payment' => [
                        'bill_id' => $billId,
                        'amount_paid' => $amountPaid,
                        'payment_date' => $paymentDate,
                        'remarks' => $remarks,
                    ],
                    'bill' => [
                        'amount_due' => max(0, $newBalance),
                        'status' => $newStatus,
                    ],
                ],
                'metadata' => [
                    'bill_id' => $billId,
                ],
            ]
        );

        return redirect()->back()->with('success', 'Payment recorded successfully');
    }
}

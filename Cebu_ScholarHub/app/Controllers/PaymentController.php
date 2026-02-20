<?php

namespace App\Controllers;

use App\Models\PaymentModel;
use App\Models\BillModel;

class PaymentController extends BaseController
{
    public function store()
    {
        $paymentModel = new PaymentModel();
        $billModel    = new BillModel();

        $billId = $this->request->getPost('bill_id');
        $amount = $this->request->getPost('amount_paid');

        // Save payment record
        $paymentModel->insert([
            'bill_id'      => $billId,
            'amount_paid'  => $amount,
            'payment_date' => $this->request->getPost('payment_date'),
            'updated_by'   => session('user_id'),
            'remarks'      => $this->request->getPost('remarks')
        ]);

        // Update bill status
        $billModel->update($billId, ['status' => 'paid']);

        return redirect()->back()->with('success', 'Payment recorded');
    }
}

<?php
 namespace App\Controllers;
  use App\Controllers\BaseController; 
  use App\Models\PaymentModel;
   use App\Models\BillModel;
    class PaymentController extends BaseController 
    { 
        public function store()
     { $paymentModel = new PaymentModel(); $billModel = new BillModel();
      $billId = $this->request->getPost('bill_id');
       $amountPaid = (float) $this->request->getPost('amount_paid'); 
       $paymentDate = $this->request->getPost('payment_date'); 
       $remarks = $this->request->getPost('remarks'); 
       $schoolId = auth_user()['school_id'];

        /** * STEP 1: Validate bill ownership */
         $bill = $billModel ->select('bills.*') 
         ->join('scholars', 'scholars.id = bills.scholar_id') 
         ->where('bills.id', $billId) 
         ->where('scholars.school_id', $schoolId)
          ->first();

           if (!$bill) 
            { return redirect()->back() ->with('error', 'Invalid bill or unauthorized access'); } 
           
           /** * STEP 2: Prevent overpayment (important) */
            if ($amountPaid > $bill['amount_due'])
                 { 
                    return redirect()->back() ->with('error', 'Payment exceeds remaining balance'); }
                    
                    /** * STEP 3: Save payment */ 
                    $paymentModel->insert([ 
                        'bill_id' => $billId, 
                        'amount_paid' => $amountPaid, 
                        'payment_date' => $paymentDate,
                         'remarks' => $remarks,
                          'updated_by' => auth_user()['id'], ]);
                          
                          /** * STEP 4: Subtract payment from amount_due */
                          
                          $newBalance = $bill['amount_due'] - $amountPaid; 
                          $billModel->update($billId, [
                             'amount_due' => max(0, $newBalance), 
                             'status' => $newBalance <= 0 ? 'paid' : 'partial', ]);
                             
                              return redirect()->back()->with('success', 'Payment recorded successfully'); 
                              }
                               }
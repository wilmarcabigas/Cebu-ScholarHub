<?php

namespace App\Controllers\School;

use App\Controllers\BaseController;
use App\Models\BillModel;
use App\Models\ScholarModel;    

class BillingController extends BaseController
{
    protected $billModel;

    public function __construct()
    {
        $this->billModel = new BillModel();
    }

    public function index()
    {
        $data['bills'] = $this->billModel
    ->select("
        bills.*,
        CONCAT(scholars.first_name, ' ', scholars.last_name) AS scholar_name
    ")
    ->join('scholars', 'scholars.id = bills.scholar_id')
    ->orderBy('bills.created_at', 'DESC')
    ->findAll();

        return view('bills/index', $data);
    }

    public function create()
    {
        // Only School roles can post billing
        if (!in_array(auth_user()['role'], ['school_admin', 'school_staff'])) {
        return redirect()->back()->with('error', 'Unauthorized access');
    }

       $scholarModel = new ScholarModel();

        $schoolId = auth_user()['school_id']; 
        $data['scholars'] = $scholarModel->getBySchoolId($schoolId);

        return view('bills/create', $data);
    }

    public function store()
    {   
        $this->billModel->insert([
            'scholar_id'     => $this->request->getPost('scholar_id'),
            'billing_period' => $this->request->getPost('billing_period'),
            'amount_due'     => $this->request->getPost('amount_due'),
            'due_date'       => $this->request->getPost('due_date'),
            'status'         => 'pending',
            'remarks'        => $this->request->getPost('remarks'),
            'posted_by'      => auth_user()['id'],
        ]);

        return redirect()->to('/school/billing')->with('success', 'Billing posted successfully');
    }

  public function view($id)
{
    $bill = $this->billModel
        ->select("
            bills.*,
            CONCAT(scholars.first_name, ' ', scholars.last_name) AS scholar_name
        ")
        ->join('scholars', 'scholars.id = bills.scholar_id')
        ->where('bills.id', $id)
        ->first();

    $paymentModel = model('PaymentModel');

    $totalPaid = $paymentModel
        ->where('bill_id', $id)
        ->selectSum('amount_paid')
        ->get()
        ->getRow()
        ->amount_paid ?? 0;

    $bill['total_paid'] = $totalPaid;
    $bill['remaining_balance'] = $bill['amount_due'] - $totalPaid;

    $data['bill'] = $bill;

    $data['payments'] = $paymentModel
        ->where('bill_id', $id)
        ->findAll();

    return view('bills/view', $data);
}

}

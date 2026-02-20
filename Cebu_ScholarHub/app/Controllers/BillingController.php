<?php

namespace App\Controllers;

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
        if (!in_array(session('role'), ['school_admin', 'school_staff'])) {
            return redirect()->back()->with('error', 'Unauthorized access');
        }

        $scholarModel = new ScholarModel();
        $data['scholars'] = $scholarModel->findAll();

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
            'posted_by'      => session('user_id')
        ]);

        return redirect()->to('/billing')->with('success', 'Billing posted successfully');
    }

    public function view($id)
    {
        $data['bill'] = $this->billModel
    ->select("
        bills.*,
        CONCAT(scholars.first_name, ' ', scholars.last_name) AS scholar_name
    ")
    ->join('scholars', 'scholars.id = bills.scholar_id')
    ->where('bills.id', $id)
    ->first();

        $data['payments'] = model('PaymentModel')
            ->where('bill_id', $id)
            ->findAll();

        return view('bills/view', $data);
    }
}

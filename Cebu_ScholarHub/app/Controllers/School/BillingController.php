<?php

namespace App\Controllers\School;

use App\Controllers\BaseController;
use App\Libraries\ActivityLogger;
use App\Libraries\ActivityNotifier;
use App\Models\BillModel;
use App\Models\ScholarModel;
use App\Models\SchoolModel;

class BillingController extends BaseController
{
    protected $billModel;
    protected $scholarModel;
    protected $schoolModel;
    protected $activityNotifier;
    protected $activityLogger;

    public function __construct()
    {
        $this->billModel = new BillModel();
        $this->scholarModel = new ScholarModel();
        $this->schoolModel = new SchoolModel();
        $this->activityNotifier = new ActivityNotifier();
        $this->activityLogger = new ActivityLogger();
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

        return view('bills/index', [
            'title' => 'Manage Billing',
            'bills' => $data['bills'],
            'show_back' => true,
            'back_url'  => site_url('dashboard')
        ]);
    }

    public function create()
    {
        // Only School roles can post billing
        if (!in_array(auth_user()['role'], ['school_admin', 'school_staff'])) {
        return redirect()->back()->with('error', 'Unauthorized access');
    }

        $schoolId = auth_user()['school_id']; 
        $data['scholars'] = $this->scholarModel->getBySchoolId($schoolId);

        return view('bills/create', [
            'show_back' => true,
            'back_url'  => site_url('/school/billing'),
            'scholars' => $data['scholars']
        ]);
    }

    public function store()
    {
        $authUser = auth_user();
        $billId = $this->billModel->insert([
            'scholar_id'     => $this->request->getPost('scholar_id'),
            'billing_period' => $this->request->getPost('billing_period'),
            'amount_due'     => $this->request->getPost('amount_due'),
            'due_date'       => $this->request->getPost('due_date'),
            'status'         => 'pending',
            'remarks'        => $this->request->getPost('remarks'),
            'posted_by'      => auth_user()['id'],
        ]);

        if ($billId) {
            $scholar = $this->scholarModel->find((int) $this->request->getPost('scholar_id'));
            $school = $this->schoolModel->find($authUser['school_id']);
            $scholarName = $scholar
                ? trim(($scholar['first_name'] ?? '') . ' ' . ($scholar['last_name'] ?? ''))
                : 'a scholar';
            $schoolName = $school['name'] ?? 'Unknown School';

            $this->activityNotifier->notifySchoolActivity(
                $authUser,
                'bill_posted',
                'New bill posted',
                "{$authUser['full_name']} posted a bill for {$scholarName} from {$schoolName}.",
                site_url('school/billing'),
                (int) $authUser['school_id']
            );

            $this->activityLogger->logSchoolAccountAction(
                $authUser,
                'bill_posted',
                'Billing posted',
                "{$authUser['full_name']} posted a bill for {$scholarName} from {$schoolName}.",
                [
                    'action' => 'create',
                    'school_id' => (int) $authUser['school_id'],
                    'subject_type' => 'bill',
                    'subject_id' => (int) $billId,
                    'new_values' => [
                        'scholar_id' => (int) $this->request->getPost('scholar_id'),
                        'billing_period' => $this->request->getPost('billing_period'),
                        'amount_due' => $this->request->getPost('amount_due'),
                        'due_date' => $this->request->getPost('due_date'),
                        'remarks' => $this->request->getPost('remarks'),
                        'status' => 'pending',
                    ],
                    'metadata' => [
                        'school_name' => $schoolName,
                        'scholar_name' => $scholarName,
                    ],
                ]
            );
        }

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

    return view('bills/view', [
        'bill' => $bill,
        'payments' => $data['payments'],
        'show_back' => true,
        'back_url'  => site_url('/school/billing'),
    ] + $data);
}

}

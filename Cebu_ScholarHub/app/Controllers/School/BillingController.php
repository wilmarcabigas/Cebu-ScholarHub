<?php

namespace App\Controllers\School;

use App\Controllers\BaseController;
use App\Libraries\ActivityLogger;
use App\Libraries\ActivityNotifier;
use App\Models\BillingBatchModel;
use App\Models\BillingItemModel;
use App\Models\PaymentModel;
use App\Models\BillModel;
use App\Models\ScholarModel;
use App\Models\SchoolModel;

class BillingController extends BaseController
{
    protected $activityNotifier;
    protected $activityLogger;
    protected $schoolModel;

    public function __construct()
    {
        $this->activityNotifier = new ActivityNotifier();
        $this->activityLogger   = new ActivityLogger();
        $this->schoolModel      = new SchoolModel();
    }

    // ------------------------------------------------------------------
    // INDEX — school user sees their billing batches
    // ------------------------------------------------------------------
    public function index()
    {
        $batchModel = new BillingBatchModel();
        $schoolId   = auth_school_id();

        $batches = $batchModel->getBatchesForSchool($schoolId);

        return view('school/billing/index', [
            'batches' => $batches,
        ]);
    }

    // ------------------------------------------------------------------
    // CREATE — show form to start a new billing batch
    // ------------------------------------------------------------------
    public function create()
    {
        $scholarModel = new ScholarModel();
        $schoolId     = auth_school_id();

        $scholars = $scholarModel
            ->where('school_id', $schoolId)
            ->where('status', 'active')
            ->orderBy('last_name', 'ASC')
            ->findAll();

        return view('school/billing/create', [
            'scholars' => $scholars,
        ]);
    }

    // ------------------------------------------------------------------
    // STORE — save the new billing batch and items
    // ------------------------------------------------------------------
    public function store()
    {
        $batchModel = new BillingBatchModel();
        $itemModel  = new BillingItemModel();

        $schoolId   = auth_school_id();
        $semester   = $this->request->getPost('semester');
        $schoolYear = $this->request->getPost('school_year');
        $batchLabel = $this->request->getPost('batch_label');
        $remarks    = $this->request->getPost('remarks');
        $scholarIds = $this->request->getPost('scholar_ids') ?? [];

        if (empty($semester) || empty($schoolYear) || empty($batchLabel)) {
            return redirect()->back()
                ->with('error', 'Missing required fields.')
                ->withInput();
        }

        if (empty($scholarIds)) {
            return redirect()->back()
                ->with('error', 'Please select at least one scholar.')
                ->withInput();
        }

        // Prevent duplicate batch for the same school + semester + school year
        if ($batchModel->duplicateExists($schoolId, $semester, $schoolYear)) {
            return redirect()->back()
                ->with('error', "A billing batch for {$semester} {$schoolYear} already exists. Please edit or delete it instead.")
                ->withInput();
        }

        $totalAmount = count($scholarIds) * 10000;

        $db = db_connect();
        $db->transStart();

        $batchId = $batchModel->insert([
            'school_id'    => $schoolId,
            'semester'     => $semester,
            'school_year'  => $schoolYear,
            'batch_label'  => $batchLabel,
            'total_amount' => $totalAmount,
            'status'       => 'draft',
            'remarks'      => $remarks,
            'submitted_by' => auth_id(),
        ], true);

        if (!$batchId) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Failed to create billing batch.');
        }

        $scholarModel = new ScholarModel();
        foreach ($scholarIds as $scholarId) {
            $scholar = $scholarModel->find($scholarId);

            if (!$scholar || $scholar['school_id'] != $schoolId) {
                $db->transRollback();
                return redirect()->back()->with('error', 'Invalid scholar selected.');
            }

            $itemModel->insert([
                'batch_id'   => $batchId,
                'scholar_id' => $scholarId,
                'control_no' => $scholar['control_no'] ?? '',
                'amount'     => 10000,
            ]);
        }

        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->back()->with('error', 'Failed to create billing.');
        }

        log_audit(auth_id(), 'CREATE', 'billing_batches', $batchId, 'Draft batch created with ' . count($scholarIds) . ' scholars.');

        $authUser = auth_user();
        $school = $this->schoolModel->find($authUser['school_id']);
        $schoolName = $school['name'] ?? 'Unknown School';

        $this->activityNotifier->notifySchoolActivity(
            $authUser,
            'batch_created',
            'New billing batch created',
            "{$authUser['full_name']} created a billing batch from {$schoolName}.",
            site_url('admin/billing'),
            (int) $authUser['school_id']
        );

        return redirect()->to(site_url('school/billing'))
            ->with('success', 'Billing batch created as draft. You can edit or submit it when ready.');
    }

    // ------------------------------------------------------------------
    // VIEW — view a specific batch with payment info
    // ------------------------------------------------------------------
    public function view(int $batchId)
    {
        $batchModel   = new BillingBatchModel();
        $itemModel    = new BillingItemModel();
        $billModel    = new BillModel();
        $paymentModel = new PaymentModel();
        $schoolId     = auth_school_id();

        $batch = $batchModel->find($batchId);
        if (!$batch || $batch['school_id'] != $schoolId) {
            return redirect()->to(site_url('school/billing'))
                ->with('error', 'Billing not found.');
        }

        $items = $itemModel->getItemsWithScholars($batchId);

        // Fetch bills and payments for this batch (available after admin receives it)
        $bills    = $billModel->where('batch_id', $batchId)->findAll();
        $payments = [];
        foreach ($bills as $bill) {
            $billPayments = $paymentModel->getPaymentsForBill($bill['id']);
            $payments     = array_merge($payments, $billPayments);
        }

        $totalAmountPaid = array_sum(array_column($bills, 'amount_paid'));

        return view('school/billing/view', [
            'batch'           => $batch,
            'items'           => $items,
            'payments'        => $payments,
            'totalAmountPaid' => $totalAmountPaid,
        ]);
    }

    // ------------------------------------------------------------------
    // EDIT — show edit form for a draft batch
    // ------------------------------------------------------------------
    public function edit(int $batchId)
    {
        $batchModel   = new BillingBatchModel();
        $itemModel    = new BillingItemModel();
        $scholarModel = new ScholarModel();
        $schoolId     = auth_school_id();

        $batch = $batchModel->find($batchId);
        if (!$batch || $batch['school_id'] != $schoolId) {
            return redirect()->to(site_url('school/billing'))
                ->with('error', 'Billing not found.');
        }

        if ($batch['status'] !== 'draft') {
            return redirect()->to(site_url('school/billing'))
                ->with('error', 'Only draft billings can be edited.');
        }

        // IDs of scholars already in this batch
        $existingItems    = $itemModel->where('batch_id', $batchId)->findAll();
        $existingScholars = array_column($existingItems, 'scholar_id');

        // All active scholars for this school
        $scholars = $scholarModel
            ->where('school_id', $schoolId)
            ->where('status', 'active')
            ->orderBy('last_name', 'ASC')
            ->findAll();

        return view('school/billing/edit', [
            'batch'            => $batch,
            'scholars'         => $scholars,
            'existingScholars' => $existingScholars,
        ]);
    }

    // ------------------------------------------------------------------
    // UPDATE — save edits to a draft batch (replace scholars)
    // ------------------------------------------------------------------
    public function update(int $batchId)
    {
        $batchModel   = new BillingBatchModel();
        $itemModel    = new BillingItemModel();
        $scholarModel = new ScholarModel();
        $schoolId     = auth_school_id();

        $batch = $batchModel->find($batchId);
        if (!$batch || $batch['school_id'] != $schoolId) {
            return redirect()->to(site_url('school/billing'))
                ->with('error', 'Billing not found.');
        }

        if ($batch['status'] !== 'draft') {
            return redirect()->back()->with('error', 'Only draft billings can be edited.');
        }

        $semester   = $this->request->getPost('semester');
        $schoolYear = $this->request->getPost('school_year');
        $batchLabel = $this->request->getPost('batch_label');
        $remarks    = $this->request->getPost('remarks');
        $scholarIds = $this->request->getPost('scholar_ids') ?? [];

        if (empty($semester) || empty($schoolYear) || empty($batchLabel)) {
            return redirect()->back()->with('error', 'Missing required fields.')->withInput();
        }

        if (empty($scholarIds)) {
            return redirect()->back()->with('error', 'Please select at least one scholar.')->withInput();
        }

        // Check duplicate — exclude the current batch from the check
        if ($batchModel->duplicateExists($schoolId, $semester, $schoolYear, $batchId)) {
            return redirect()->back()
                ->with('error', "Another billing batch for {$semester} {$schoolYear} already exists.")
                ->withInput();
        }

        $totalAmount = count($scholarIds) * 10000;

        $db = db_connect();
        $db->transStart();

        // Replace all items
        $itemModel->deleteByBatch($batchId);

        foreach ($scholarIds as $scholarId) {
            $scholar = $scholarModel->find($scholarId);

            if (!$scholar || $scholar['school_id'] != $schoolId) {
                $db->transRollback();
                return redirect()->back()->with('error', 'Invalid scholar selected.');
            }

            $itemModel->insert([
                'batch_id'   => $batchId,
                'scholar_id' => $scholarId,
                'control_no' => $scholar['control_no'] ?? '',
                'amount'     => 10000,
            ]);
        }

        $batchModel->update($batchId, [
            'semester'     => $semester,
            'school_year'  => $schoolYear,
            'batch_label'  => $batchLabel,
            'remarks'      => $remarks,
            'total_amount' => $totalAmount,
            // Clear rejection remarks when school edits after rejection
            'rejection_remarks' => null,
        ]);

        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->back()->with('error', 'Failed to update billing.');
        }

        log_audit(auth_id(), 'EDIT', 'billing_batches', $batchId, 'Draft batch updated with ' . count($scholarIds) . ' scholars.');

        return redirect()->to(site_url('school/billing/view/' . $batchId))
            ->with('success', 'Billing batch updated successfully.');
    }

    // ------------------------------------------------------------------
    // SUBMIT — school submits a draft billing to the office
    // ------------------------------------------------------------------
    public function submit(int $batchId)
    {
        $batchModel = new BillingBatchModel();
        $schoolId   = auth_school_id();

        $batch = $batchModel->find($batchId);
        if (!$batch || $batch['school_id'] != $schoolId) {
            return redirect()->back()->with('error', 'Billing not found.');
        }

        if ($batch['status'] !== 'draft') {
            return redirect()->back()
                ->with('error', 'Only draft billings can be submitted.');
        }

        $batchModel->update($batchId, [
            'status'       => 'submitted',
            'submitted_at' => date('Y-m-d H:i:s'),
            'submitted_by' => auth_id(),
        ]);

        log_audit(auth_id(), 'SUBMIT', 'billing_batches', $batchId, 'Batch submitted to office.');

        return redirect()->back()
            ->with('success', 'Billing submitted to the office successfully.');
    }

    // ------------------------------------------------------------------
    // CONFIRM RECEIPT — school confirms they received the payment
    // ------------------------------------------------------------------
    public function confirmReceipt(int $batchId)
    {
        $batchModel = new BillingBatchModel();
        $schoolId   = auth_school_id();

        $batch = $batchModel->find($batchId);
        if (!$batch || $batch['school_id'] != $schoolId) {
            return redirect()->back()->with('error', 'Billing not found.');
        }

        if (!in_array($batch['status'], ['partial', 'paid'])) {
            return redirect()->back()
                ->with('error', 'Receipt can only be confirmed once a payment has been recorded.');
        }

        if (!empty($batch['receipt_confirmed_at'])) {
            return redirect()->back()
                ->with('error', 'Receipt has already been confirmed.');
        }

        $batchModel->update($batchId, [
            'receipt_confirmed_at' => date('Y-m-d H:i:s'),
            'receipt_confirmed_by' => auth_id(),
        ]);

        log_audit(auth_id(), 'CONFIRM_RECEIPT', 'billing_batches', $batchId, 'School confirmed receipt of payment.');

        return redirect()->back()
            ->with('success', 'Receipt confirmed. Payment has been acknowledged.');
    }

    // ------------------------------------------------------------------
    // DELETE — delete a draft billing batch
    // ------------------------------------------------------------------
    public function delete(int $batchId)
    {
        $batchModel = new BillingBatchModel();
        $itemModel  = new BillingItemModel();
        $schoolId   = auth_school_id();

        $batch = $batchModel->find($batchId);
        if (!$batch || $batch['school_id'] != $schoolId) {
            return redirect()->back()->with('error', 'Billing not found.');
        }

        if ($batch['status'] !== 'draft') {
            return redirect()->back()
                ->with('error', 'Only draft billings can be deleted.');
        }

        $db = db_connect();
        $db->transStart();

        $itemModel->deleteByBatch($batchId);
        $batchModel->delete($batchId);

        $db->transComplete();

        log_audit(auth_id(), 'DELETE', 'billing_batches', $batchId, 'Draft batch deleted.');

        return redirect()->to(site_url('school/billing'))
            ->with('success', 'Billing deleted successfully.');
    }

    // ------------------------------------------------------------------
    // PRINT — printable billing sheet for the school
    // ------------------------------------------------------------------
    public function print(int $batchId)
    {
        $batchModel = new BillingBatchModel();
        $itemModel  = new BillingItemModel();
        $schoolId   = auth_school_id();

        $batch = $batchModel->find($batchId);
        if (!$batch || $batch['school_id'] != $schoolId) {
            return redirect()->to(site_url('school/billing'))
                ->with('error', 'Billing not found.');
        }

        $items = $itemModel->getItemsWithScholars($batchId);

        return view('bills/print', [
            'batch' => $batch,
            'items' => $items,
        ]);
    }
}

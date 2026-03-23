<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\SchoolModel;
use App\Models\BillModel;
use App\Models\MessageModel;
use App\Models\ScholarModel;
use App\Models\BillingBatchModel;

class Dashboard extends BaseController
{
    protected $userModel;
    protected $schoolModel;
    protected $billingModel;
    protected $messageModel;
    protected $scholarModel;
    protected $batchModel;

    public function __construct()
    {
        $this->userModel    = new UserModel();
        $this->schoolModel  = new SchoolModel();
        $this->billingModel = new BillModel();
        $this->messageModel = new MessageModel();
        $this->scholarModel = new ScholarModel();
        $this->batchModel   = new BillingBatchModel();
    }

    public function index()
    {
        $user = auth_user();
        if (!$user) {
            return redirect()->to('login');
        }

        $data = [
            'title' => ucfirst($user['role']) . ' Dashboard',
            'user'  => $user,
        ];

        // Attach school info for school-level users
        if (in_array($user['role'], ['school_admin', 'school_staff']) && $user['school_id']) {
            $data['school'] = $this->schoolModel->find($user['school_id']);
        }

        switch ($user['role']) {

            /* ================================================================
             * ADMIN DASHBOARD
             * ================================================================ */
            case 'admin':

                // Billing financial totals from bills table
                $bills          = $this->billingModel->findAll();
                $totalBilled    = array_sum(array_column($bills, 'amount_due'));
                $totalCollected = array_sum(array_column($bills, 'amount_paid'));

                // Pending billing submissions (submitted but not yet received)
                $pendingBatches = $this->batchModel
                    ->select('billing_batches.id, billing_batches.school_id, billing_batches.batch_label, billing_batches.semester, billing_batches.school_year, billing_batches.total_amount, billing_batches.status, billing_batches.created_at, billing_batches.updated_at, billing_batches.submitted_at, schools.name AS school_name')
                    ->join('schools', 'schools.id = billing_batches.school_id')
                    ->where('billing_batches.status', 'submitted')
                    ->orderBy('billing_batches.submitted_at', 'DESC')
                    ->findAll(5);

                // Recent activity — last 5 scholar records added
                $recentActivity = db_connect()->query("
                    SELECT
                        'New scholar added' AS description,
                        CONCAT(s.first_name, ' ', s.last_name, ' (', IFNULL(s.course,''), ')') AS detail,
                        CASE
                            WHEN s.created_at >= NOW() - INTERVAL 1 HOUR
                                THEN 'Just now'
                            WHEN s.created_at >= NOW() - INTERVAL 24 HOUR
                                THEN CONCAT(TIMESTAMPDIFF(HOUR, s.created_at, NOW()), ' hours ago')
                            ELSE DATE_FORMAT(s.created_at, '%b %d, %Y')
                        END AS time_ago
                    FROM scholars s
                    ORDER BY s.created_at DESC
                    LIMIT 5
                ")->getResultArray();

                $data['stats'] = [
                    'total_scholars'  => $this->scholarModel->where('status', 'active')->countAllResults(),
                    'active_schools'  => $this->schoolModel->countAllResults(),
                    'pending_bills'   => $this->batchModel->where('status', 'submitted')->countAllResults(),
                    'messages'        => $this->messageModel->where('receiver_id', $user['id'])->where('is_read', 0)->countAllResults(),
                    'total_billed'    => $totalBilled,
                    'total_collected' => $totalCollected,
                ];

                $data['pendingBatches'] = $pendingBatches;
                $data['recentActivity'] = $recentActivity;

                return view('dashboard/admin', $data);


            /* ================================================================
             * STAFF DASHBOARD
             * ================================================================ */
            case 'staff':

                // Pending billing submissions widget
                $pendingBatches = $this->batchModel
                    ->select('billing_batches.id, billing_batches.school_id, billing_batches.batch_label, billing_batches.semester, billing_batches.school_year, billing_batches.status, billing_batches.created_at, billing_batches.updated_at, billing_batches.submitted_at, schools.name AS school_name')
                    ->join('schools', 'schools.id = billing_batches.school_id')
                    ->where('billing_batches.status', 'submitted')
                    ->orderBy('billing_batches.submitted_at', 'DESC')
                    ->findAll(5);

                // Recent messages for the inbox widget
                $recentMessages = db_connect()->query("
                    SELECT
                        m.message_body,
                        u.full_name AS sender_name,
                        CASE
                            WHEN m.sent_at >= NOW() - INTERVAL 1 HOUR
                                THEN 'Just now'
                            WHEN m.sent_at >= NOW() - INTERVAL 24 HOUR
                                THEN CONCAT(TIMESTAMPDIFF(HOUR, m.sent_at, NOW()), ' hours ago')
                            ELSE DATE_FORMAT(m.sent_at, '%b %d, %Y')
                        END AS time_ago
                    FROM messages m
                    JOIN users u ON u.id = m.sender_id
                    WHERE m.receiver_id = " . (int) $user['id'] . "
                    ORDER BY m.sent_at DESC
                    LIMIT 5
                ")->getResultArray();

                $data['stats'] = [
                    'pending_reviews'  => $this->billingModel->where('status', 'pending')->countAllResults(),
                    'new_applications' => $this->scholarModel->where('created_at >=', date('Y-m-d', strtotime('-7 days')))->countAllResults(),
                    'school_updates'   => $this->batchModel->where('status', 'submitted')->countAllResults(),
                    'messages'         => $this->messageModel->where('receiver_id', $user['id'])->where('is_read', 0)->countAllResults(),
                ];

                $data['pendingBatches'] = $pendingBatches;
                $data['recentMessages'] = $recentMessages;

                return view('dashboard/staff', $data);


            /* ================================================================
             * SCHOOL ADMIN DASHBOARD
             * ================================================================ */
            case 'school_admin':

                $schoolId = $user['school_id'];

                // Recent billing batches submitted by this school
                $myBatches = $this->batchModel
                    ->select('billing_batches.id, billing_batches.school_id, billing_batches.batch_label, billing_batches.semester, billing_batches.school_year, billing_batches.total_amount, billing_batches.status, billing_batches.created_at, billing_batches.updated_at, schools.name AS school_name')
                    ->join('schools', 'schools.id = billing_batches.school_id')
                    ->where('billing_batches.school_id', $schoolId)
                    ->orderBy('billing_batches.created_at', 'DESC')
                    ->findAll(5);

                // Financial summary for this school only
                $schoolBills    = $this->billingModel->where('school_id', $schoolId)->findAll();
                $totalBilled    = array_sum(array_column($schoolBills, 'amount_due'));
                $totalCollected = array_sum(array_column($schoolBills, 'amount_paid'));

                $data['stats'] = [
                    'active_scholars' => $this->scholarModel
                        ->where('school_id', $schoolId)
                        ->countAllResults(),

                    'pending_bills' => $this->batchModel
                        ->where('school_id', $schoolId)
                        ->where('status', 'submitted')
                        ->countAllResults(),

                    'pending_approval' => $this->batchModel
                        ->where('school_id', $schoolId)
                        ->where('status', 'draft')
                        ->countAllResults(),

                    'messages' => $this->messageModel
                        ->where('receiver_id', $user['id'])
                        ->where('is_read', 0)
                        ->countAllResults(),

                    'total_billed'    => $totalBilled,
                    'total_collected' => $totalCollected,
                ];

                $data['myBatches'] = $myBatches;

                return view('dashboard/school_admin', $data);


            /* ================================================================
             * SCHOOL STAFF DASHBOARD
             * ================================================================ */
            case 'school_staff':

                $schoolId = $user['school_id'];

                // Recent billing batches for this school
                $myBatches = $this->batchModel
                    ->select('billing_batches.id, billing_batches.school_id, billing_batches.batch_label, billing_batches.semester, billing_batches.school_year, billing_batches.total_amount, billing_batches.status, billing_batches.created_at, billing_batches.updated_at, schools.name AS school_name')
                    ->join('schools', 'schools.id = billing_batches.school_id')
                    ->where('billing_batches.school_id', $schoolId)
                    ->orderBy('billing_batches.created_at', 'DESC')
                    ->findAll(5);

                $data['stats'] = [
                    'active_scholars' => $this->scholarModel
                        ->where('school_id', $schoolId)
                        ->where('status', 'active')
                        ->countAllResults(),

                    'pending_bills' => $this->batchModel
                        ->where('school_id', $schoolId)
                        ->where('status', 'submitted')
                        ->countAllResults(),

                    'messages' => $this->messageModel
                        ->where('receiver_id', $user['id'])
                        ->where('is_read', 0)
                        ->countAllResults(),

                    'requirements_due' => 0,
                ];

                $data['myBatches'] = $myBatches;

                return view('dashboard/school_staff', $data);


            /* ================================================================
             * SCHOLAR DASHBOARD
             * ================================================================ */
            case 'scholar':
                $data['scholar_data'] = $this->userModel
                    ->select('users.id, users.school_id, users.first_name, users.middle_name, users.last_name, users.email, users.role, users.contact_no, users.address, users.created_at, users.updated_at, schools.name as school_name')
                    ->join('schools', 'schools.id = users.school_id', 'left')
                    ->find($user['id']);

                return view('dashboard/scholar', $data);

            default:
                return redirect()->to('logout');
        }
    }


    /* =====================================================
       LIVE SYSTEM SUMMARY (ADDED — DOES NOT CHANGE LOGIC)
       ===================================================== */

    public function liveStats()
    {
        $user = auth_user();

        if (!$user) {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        $stats = [
            'total_scholars' =>
                $this->scholarModel->where('status', 'active')->countAllResults(),

            'active_schools' =>
                $this->schoolModel->countAllResults(),

            'pending_bills' =>
                $this->billingModel->where('status', 'pending')->countAllResults(),

            'messages' =>
                $this->messageModel
                    ->where('receiver_id', $user['id'])
                    ->where('is_read', 0)
                    ->countAllResults()
        ];

        return $this->response->setJSON([
    'status' => 'success',
    'stats' => $stats
]);
    }

}
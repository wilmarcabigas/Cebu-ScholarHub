<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\SchoolModel;
use App\Models\BillModel;
use App\Models\MessageModel;
use App\Models\ScholarModel;
use App\Models\BillingBatchModel;
use App\Models\ActivityNotificationModel;

class Dashboard extends BaseController
{
    protected $userModel;
    protected $schoolModel;
    protected $billingModel;
    protected $messageModel;
    protected $scholarModel;
    protected $batchModel;
    protected $activityNotificationModel;

    public function __construct()
    {
        $this->userModel    = new UserModel();
        $this->schoolModel  = new SchoolModel();
        $this->billingModel = new BillModel();
        $this->messageModel = new MessageModel();
        $this->scholarModel = new ScholarModel();
        $this->batchModel                = new BillingBatchModel();
        $this->activityNotificationModel = new ActivityNotificationModel();
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
        if (in_array($user['role'], ['school_admin', 'school_staff']) && !empty($user['school_id'])) {
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

                $data['school_activity_count'] = $this->activityNotificationModel->countUnreadForRecipient((int) $user['id']);
                $data['school_activity_notifications'] = $this->activityNotificationModel->getRecentForRecipient((int) $user['id']);

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

                // --- New dashboard enhancements ---
                $db = db_connect();

                // Monthly enrollment trend (last 12 months)
                $monthlyEnrollment = $db->query("
                    SELECT DATE_FORMAT(created_at, '%b %Y') as month, COUNT(*) as count
                    FROM scholars
                    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                      AND deleted_at IS NULL
                    GROUP BY DATE_FORMAT(created_at, '%Y-%m'), DATE_FORMAT(created_at, '%b %Y')
                    ORDER BY MIN(created_at) ASC
                ")->getResultArray();
                $data['enrollment_labels'] = array_column($monthlyEnrollment, 'month');
                $data['enrollment_counts'] = array_map('intval', array_column($monthlyEnrollment, 'count'));

                // Month-over-month for scholars added
                $lastMonthStr    = date('Y-m', strtotime('-1 month'));
                $currentMonthStr = date('Y-m');
                $lastMonthScholars = (int) $db->query(
                    "SELECT COUNT(*) as c FROM scholars WHERE DATE_FORMAT(created_at,'%Y-%m')=? AND deleted_at IS NULL",
                    [$lastMonthStr]
                )->getRow()->c;
                $thisMonthScholars = (int) $db->query(
                    "SELECT COUNT(*) as c FROM scholars WHERE DATE_FORMAT(created_at,'%Y-%m')=? AND deleted_at IS NULL",
                    [$currentMonthStr]
                )->getRow()->c;
                $data['mom_scholars'] = $lastMonthScholars > 0
                    ? round((($thisMonthScholars - $lastMonthScholars) / $lastMonthScholars) * 100, 1)
                    : null;

                // Month-over-month for submitted billing batches
                $lastMonthBills = (int) $db->query(
                    "SELECT COUNT(*) as c FROM billing_batches WHERE status='submitted' AND DATE_FORMAT(submitted_at,'%Y-%m')=?",
                    [$lastMonthStr]
                )->getRow()->c;
                $thisMonthBills = (int) $db->query(
                    "SELECT COUNT(*) as c FROM billing_batches WHERE status='submitted' AND DATE_FORMAT(submitted_at,'%Y-%m')=?",
                    [$currentMonthStr]
                )->getRow()->c;
                $data['mom_bills'] = $lastMonthBills > 0
                    ? round((($thisMonthBills - $lastMonthBills) / $lastMonthBills) * 100, 1)
                    : null;

                // Needs Attention: batches pending > 7 days
                $data['attention_batches'] = $db->query("
                    SELECT bb.id, bb.batch_label, bb.semester, bb.school_year,
                           bb.total_amount, bb.submitted_at,
                           DATEDIFF(NOW(), bb.submitted_at) AS days_pending,
                           s.name AS school_name
                    FROM billing_batches bb
                    JOIN schools s ON bb.school_id = s.id
                    WHERE bb.status = 'submitted'
                      AND DATEDIFF(NOW(), bb.submitted_at) > 7
                    ORDER BY days_pending DESC
                    LIMIT 5
                ")->getResultArray();

                // Needs Attention: on-hold or disqualified scholars
                $data['attention_scholars'] = (int) $db->query(
                    "SELECT COUNT(*) as c FROM scholars WHERE status IN ('on-hold','disqualified') AND deleted_at IS NULL"
                )->getRow()->c;

                $schoolChartData = $this->scholarModel
                    ->select('schools.name as school_name, COUNT(scholars.id) as total')
                    ->join('schools', 'schools.id = scholars.school_id', 'left')
                    ->groupBy('schools.id, schools.name')
                    ->orderBy('total', 'DESC')
                    ->findAll();

                $data['school_chart_labels'] = [];
                $data['school_chart_totals'] = [];

                foreach ($schoolChartData as $row) {
                    $data['school_chart_labels'][] = !empty($row['school_name']) ? $row['school_name'] : 'No School';
                    $data['school_chart_totals'][] = (int) $row['total'];
                }

                $courseChartData = $this->scholarModel
                    ->select('course, COUNT(id) as total')
                    ->groupBy('course')
                    ->orderBy('total', 'DESC')
                    ->findAll();

                $data['course_chart_labels'] = [];
                $data['course_chart_totals'] = [];

                foreach ($courseChartData as $row) {
                    $data['course_chart_labels'][] = !empty($row['course']) ? $row['course'] : 'No Course';
                    $data['course_chart_totals'][] = (int) $row['total'];
                }

                $statusChartData = $this->scholarModel
                    ->select('status, COUNT(id) as total')
                    ->groupBy('status')
                    ->orderBy('status', 'ASC')
                    ->findAll();

                $data['status_chart_labels'] = [];
                $data['status_chart_totals'] = [];

                foreach ($statusChartData as $row) {
                    $data['status_chart_labels'][] = !empty($row['status']) ? ucfirst($row['status']) : 'No Status';
                    $data['status_chart_totals'][] = (int) $row['total'];
                }

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

                $data['school_activity_count'] = $this->activityNotificationModel->countUnreadForRecipient((int) $user['id']);
                $data['school_activity_notifications'] = $this->activityNotificationModel->getRecentForRecipient((int) $user['id']);

                $data['stats'] = [
                    'pending_reviews'  => $this->billingModel->where('status', 'pending')->countAllResults(),
                    'new_applications' => $this->scholarModel->where('created_at >=', date('Y-m-d', strtotime('-7 days')))->countAllResults(),
                    'school_updates'   => $this->batchModel->where('status', 'submitted')->countAllResults(),
                    'messages'         => $this->messageModel->where('receiver_id', $user['id'])->where('is_read', 0)->countAllResults(),
                ];

                $data['pendingBatches'] = $pendingBatches;
                $data['recentMessages'] = $recentMessages;

                $schoolChartData = $this->scholarModel
                    ->select('schools.name as school_name, COUNT(scholars.id) as total')
                    ->join('schools', 'schools.id = scholars.school_id', 'left')
                    ->groupBy('schools.id, schools.name')
                    ->orderBy('total', 'DESC')
                    ->findAll();

                $data['school_chart_labels'] = [];
                $data['school_chart_totals'] = [];

                foreach ($schoolChartData as $row) {
                    $data['school_chart_labels'][] = !empty($row['school_name']) ? $row['school_name'] : 'No School';
                    $data['school_chart_totals'][] = (int) $row['total'];
                }

                $courseChartData = $this->scholarModel
                    ->select('course, COUNT(id) as total')
                    ->groupBy('course')
                    ->orderBy('total', 'DESC')
                    ->findAll();

                $data['course_chart_labels'] = [];
                $data['course_chart_totals'] = [];

                foreach ($courseChartData as $row) {
                    $data['course_chart_labels'][] = !empty($row['course']) ? $row['course'] : 'No Course';
                    $data['course_chart_totals'][] = (int) $row['total'];
                }

                $statusChartData = $this->scholarModel
                    ->select('status, COUNT(id) as total')
                    ->groupBy('status')
                    ->orderBy('status', 'ASC')
                    ->findAll();

                $data['status_chart_labels'] = [];
                $data['status_chart_totals'] = [];

                foreach ($statusChartData as $row) {
                    $data['status_chart_labels'][] = !empty($row['status']) ? ucfirst($row['status']) : 'No Status';
                    $data['status_chart_totals'][] = (int) $row['total'];
                }

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

                $courseChartData = $this->scholarModel
                    ->select('course, COUNT(id) as total')
                    ->where('school_id', $schoolId)
                    ->groupBy('course')
                    ->orderBy('total', 'DESC')
                    ->findAll();

                $data['course_chart_labels'] = [];
                $data['course_chart_totals'] = [];

                foreach ($courseChartData as $row) {
                    $data['course_chart_labels'][] = !empty($row['course']) ? $row['course'] : 'No Course';
                    $data['course_chart_totals'][] = (int) $row['total'];
                }

                $statusChartData = $this->scholarModel
                    ->select('status, COUNT(id) as total')
                    ->where('school_id', $schoolId)
                    ->groupBy('status')
                    ->orderBy('status', 'ASC')
                    ->findAll();

                $data['status_chart_labels'] = [];
                $data['status_chart_totals'] = [];

                foreach ($statusChartData as $row) {
                    $data['status_chart_labels'][] = !empty($row['status']) ? ucfirst($row['status']) : 'No Status';
                    $data['status_chart_totals'][] = (int) $row['total'];
                }

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

                $courseChartData = $this->scholarModel
                    ->select('course, COUNT(id) as total')
                    ->where('school_id', $schoolId)
                    ->groupBy('course')
                    ->orderBy('total', 'DESC')
                    ->findAll();

                $data['course_chart_labels'] = [];
                $data['course_chart_totals'] = [];

                foreach ($courseChartData as $row) {
                    $data['course_chart_labels'][] = !empty($row['course']) ? $row['course'] : 'No Course';
                    $data['course_chart_totals'][] = (int) $row['total'];
                }

                $statusChartData = $this->scholarModel
                    ->select('status, COUNT(id) as total')
                    ->where('school_id', $schoolId)
                    ->groupBy('status')
                    ->orderBy('status', 'ASC')
                    ->findAll();

                $data['status_chart_labels'] = [];
                $data['status_chart_totals'] = [];

                foreach ($statusChartData as $row) {
                    $data['status_chart_labels'][] = !empty($row['status']) ? ucfirst($row['status']) : 'No Status';
                    $data['status_chart_totals'][] = (int) $row['total'];
                }

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

    public function liveStats()
    {
        $user = session()->get('auth_user');

        if (!$user) {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        $stats = [];
        $schoolLabels = [];
        $schoolTotals = [];
        $courseLabels = [];
        $courseTotals = [];
        $statusLabels = [];
        $statusTotals = [];

        switch ($user['role']) {

            case 'admin':
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

                $schoolChartData = $this->schoolModel
                    ->select('schools.name as school_name, COUNT(scholars.id) as total')
                    ->join('scholars', 'scholars.school_id = schools.id', 'left')
                    ->groupBy('schools.id, schools.name')
                    ->orderBy('schools.name', 'ASC')
                    ->findAll();

                foreach ($schoolChartData as $row) {
                    $schoolLabels[] = !empty($row['school_name']) ? $row['school_name'] : 'No School';
                    $schoolTotals[] = (int) $row['total'];
                }

                $courseChartData = $this->scholarModel
                    ->select('course, COUNT(id) as total')
                    ->groupBy('course')
                    ->orderBy('course', 'ASC')
                    ->findAll();

                foreach ($courseChartData as $row) {
                    $courseLabels[] = !empty($row['course']) ? $row['course'] : 'No Course';
                    $courseTotals[] = (int) $row['total'];
                }

                $statusChartData = $this->scholarModel
                    ->select('status, COUNT(id) as total')
                    ->groupBy('status')
                    ->orderBy('status', 'ASC')
                    ->findAll();

                foreach ($statusChartData as $row) {
                    $statusLabels[] = !empty($row['status']) ? ucfirst($row['status']) : 'No Status';
                    $statusTotals[] = (int) $row['total'];
                }

                break;

            case 'staff':
                $stats = [
                    'pending_reviews' =>
                        $this->billingModel->where('status', 'pending')->countAllResults(),

                    'new_applications' =>
                        $this->scholarModel->where('created_at >=', date('Y-m-d', strtotime('-7 days')))
                                           ->countAllResults(),

                    'school_updates' =>
                        $this->activityNotificationModel->countUnreadForRecipient((int) $user['id']),

                    'messages' =>
                        $this->messageModel
                            ->where('receiver_id', $user['id'])
                            ->where('is_read', 0)
                            ->countAllResults()
                ];

                $schoolChartData = $this->schoolModel
                    ->select('schools.name as school_name, COUNT(scholars.id) as total')
                    ->join('scholars', 'scholars.school_id = schools.id', 'left')
                    ->groupBy('schools.id, schools.name')
                    ->orderBy('schools.name', 'ASC')
                    ->findAll();

                foreach ($schoolChartData as $row) {
                    $schoolLabels[] = !empty($row['school_name']) ? $row['school_name'] : 'No School';
                    $schoolTotals[] = (int) $row['total'];
                }

                $courseChartData = $this->scholarModel
                    ->select('course, COUNT(id) as total')
                    ->groupBy('course')
                    ->orderBy('course', 'ASC')
                    ->findAll();

                foreach ($courseChartData as $row) {
                    $courseLabels[] = !empty($row['course']) ? $row['course'] : 'No Course';
                    $courseTotals[] = (int) $row['total'];
                }

                $statusChartData = $this->scholarModel
                    ->select('status, COUNT(id) as total')
                    ->groupBy('status')
                    ->orderBy('status', 'ASC')
                    ->findAll();

                foreach ($statusChartData as $row) {
                    $statusLabels[] = !empty($row['status']) ? ucfirst($row['status']) : 'No Status';
                    $statusTotals[] = (int) $row['total'];
                }

                break;

            case 'school_admin':
            case 'school_staff':
                $schoolId = $user['school_id'];

                $stats = [
                    'active_scholars' =>
                        $this->scholarModel
                            ->where('school_id', $schoolId)
                            ->where('status', 'active')
                            ->countAllResults(),

                    'pending_bills' =>
                        $this->billingModel
                            ->select('bills.*')
                            ->join('scholars', 'scholars.id = bills.scholar_id')
                            ->where('scholars.school_id', $schoolId)
                            ->where('bills.status', 'pending')
                            ->countAllResults(),

                    'messages' =>
                        $this->messageModel
                            ->where('receiver_id', $user['id'])
                            ->where('is_read', 0)
                            ->countAllResults()
                ];

                if ($user['role'] === 'school_staff') {
                    $stats['requirements_due'] = 0;
                }

                $school = $this->schoolModel->find($schoolId);
                $schoolName = $school['name'] ?? 'My School';

                $schoolScholarTotal = $this->scholarModel
                    ->where('school_id', $schoolId)
                    ->countAllResults();

                $schoolLabels[] = $schoolName;
                $schoolTotals[] = (int) $schoolScholarTotal;

                $courseChartData = $this->scholarModel
                    ->select('course, COUNT(id) as total')
                    ->where('school_id', $schoolId)
                    ->groupBy('course')
                    ->orderBy('course', 'ASC')
                    ->findAll();

                foreach ($courseChartData as $row) {
                    $courseLabels[] = !empty($row['course']) ? $row['course'] : 'No Course';
                    $courseTotals[] = (int) $row['total'];
                }

                $statusChartData = $this->scholarModel
                    ->select('status, COUNT(id) as total')
                    ->where('school_id', $schoolId)
                    ->groupBy('status')
                    ->orderBy('status', 'ASC')
                    ->findAll();

                foreach ($statusChartData as $row) {
                    $statusLabels[] = !empty($row['status']) ? ucfirst($row['status']) : 'No Status';
                    $statusTotals[] = (int) $row['total'];
                }

                break;

            default:
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid role'
                ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'stats' => $stats,
            'school_chart' => [
                'labels' => $schoolLabels,
                'totals' => $schoolTotals
            ],
            'course_chart' => [
                'labels' => $courseLabels,
                'totals' => $courseTotals
            ],
            'status_chart' => [
                'labels' => $statusLabels,
                'totals' => $statusTotals
            ]
        ]);
    }
}

<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\SchoolModel;
use App\Models\BillModel;
use App\Models\MessageModel;
use App\Models\ScholarModel;

class Dashboard extends BaseController
{
    protected $userModel;
    protected $schoolModel;
    protected $billingModel;
    protected $messageModel;
    protected $scholarModel;

    public function __construct()
    {
        $this->userModel    = new UserModel();
        $this->schoolModel  = new SchoolModel();
        $this->billingModel = new BillModel();
        $this->messageModel = new MessageModel();
        $this->scholarModel = new ScholarModel();
    }

    public function index()
    {
        $user = auth_user();
        if (!$user) {
            return redirect()->to('login');
        }

        $data = [
            'title' => ucfirst($user['role']) . ' Dashboard',
            'user'  => $user
        ];

        if (in_array($user['role'], ['school_admin', 'school_staff']) && !empty($user['school_id'])) {
            $data['school'] = $this->schoolModel->find($user['school_id']);
        }

        switch ($user['role']) {

            /* ================= ADMIN DASHBOARD ================= */
            case 'admin':
                $data['stats'] = [
                    'total_scholars' =>
                        $this->scholarModel->where('status', 'active')->countAllResults(),

                    'active_schools' =>
                        $this->schoolModel->countAllResults(),

                    'pending_bills' =>
                        $this->billingModel->where('status', 'pending')->countAllResults(),

                    'messages' =>
                        $this->messageModel->where('receiver_id', $user['id'])
                                           ->where('is_read', 0)
                                           ->countAllResults()
                ];

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

            /* ================= STAFF DASHBOARD ================= */
            case 'staff':
                $data['stats'] = [
                    'pending_reviews' =>
                        $this->billingModel->where('status', 'pending')->countAllResults(),

                    'new_applications' =>
                        $this->scholarModel->where('created_at >=', date('Y-m-d', strtotime('-7 days')))
                                           ->countAllResults(),

                    'school_updates' =>
                        $this->billingModel->countAllResults(),

                    'messages' =>
                        $this->messageModel->where('receiver_id', $user['id'])
                                           ->where('is_read', 0)
                                           ->countAllResults()
                ];

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

            /* ================= SCHOOL ADMIN ================= */
            case 'school_admin':

                $schoolId = $user['school_id'];

                $data['stats'] = [
                    'active_scholars' =>
                        $this->scholarModel
                            ->where('school_id', $schoolId)
                            ->countAllResults(),

                    'pending_bills' =>
                        $this->billingModel
                            ->select('bills.*')
                            ->join('scholars', 'scholars.id = bills.scholar_id')
                            ->where('scholars.school_id', $schoolId)
                            ->where('bills.status', 'pending')
                            ->countAllResults(),

                    'pending_approval' =>
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

            /* ================= SCHOOL STAFF ================= */
            case 'school_staff':

                $schoolId = $user['school_id'];

                $data['stats'] = [
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
                            ->countAllResults(),

                    'requirements_due' => 0
                ];

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

            /* ================= SCHOLAR ================= */
            case 'scholar':

                $data['scholar_data'] =
                    $this->userModel->select('users.*, schools.name as school_name')
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
                        $this->billingModel->countAllResults(),

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
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

        // Add school info if school user
        if (in_array($user['role'], ['school_admin', 'school_staff']) && $user['school_id']) {
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

                return view('dashboard/staff', $data);


            /* ================= SCHOOL ADMIN ================= */
            case 'school_admin':

                $data['stats'] = [
                    'active_scholars' =>
                        $this->scholarModel
                            ->where('school_id', $user['school_id'])
                            ->countAllResults(),

                    'pending_bills' =>
                        $this->billingModel
                            ->select('bills.*')
                            ->join('scholars', 'scholars.id = bills.scholar_id')
                            ->where('scholars.school_id', $user['school_id'])
                            ->where('bills.status', 'pending')
                            ->countAllResults(),

                    'pending_approval' =>
                        $this->billingModel
                            ->select('bills.*')
                            ->join('scholars', 'scholars.id = bills.scholar_id')
                            ->where('scholars.school_id', $user['school_id'])
                            ->where('bills.status', 'pending')
                            ->countAllResults(),

                    'messages' =>
                        $this->messageModel
                            ->where('receiver_id', $user['id'])
                            ->where('is_read', 0)
                            ->countAllResults()
                ];

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
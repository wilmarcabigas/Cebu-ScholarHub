<?php


namespace App\Controllers;

use App\Models\UserModel;
use App\Models\SchoolModel;

class Dashboard extends BaseController
{
    protected $userModel;
    protected $schoolModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->schoolModel = new SchoolModel();
    }

    public function index()
    {
        $user = auth_user();
        if (!$user) {
            return redirect()->to('login');
        }

        $data = [
            'title' => ucfirst($user['role']) . ' Dashboard',
            'user' => $user
        ];

        // Add school data for school roles
        if (in_array($user['role'], ['school_admin', 'school_staff']) && $user['school_id']) {
            $data['school'] = $this->schoolModel->find($user['school_id']);
        }

        // Redirect to role-specific dashboard
        switch ($user['role']) {
            case 'admin':
                $data['stats'] = [
                    'total_scholars' => $this->userModel->where('role', 'scholar')->countAllResults(),
                    'active_schools' => $this->schoolModel->countAllResults(),
                    'pending_bills' => 0, // Add billing model count later
                    'messages' => 0 // Add message model count later
                ];
                return view('dashboard/admin', $data);

            case 'staff':
                $data['stats'] = [
                    'pending_reviews' => 0,
                    'new_applications' => 0,
                    'school_updates' => 0,
                    'messages' => 0
                ];
                return view('dashboard/staff', $data);

            case 'school_admin':
                $data['stats'] = [
                    'active_scholars' => $this->userModel->where('school_id', $user['school_id'])
                                                       ->where('role', 'scholar')
                                                       ->countAllResults(),
                    'pending_bills' => 0,
                    'pending_approval' => 0,
                    'messages' => 0
                ];
                return view('dashboard/school_admin', $data);

            case 'school_staff':
                $data['stats'] = [
                    'active_scholars' => $this->userModel->where('school_id', $user['school_id'])
                                                       ->where('role', 'scholar')
                                                       ->countAllResults(),
                    'pending_bills' => 0,
                    'requirements_due' => 0
                ];
                return view('dashboard/school_staff', $data);

            case 'scholar':
                $data['scholar_data'] = $this->userModel->select('users.*, schools.name as school_name')
                                                      ->join('schools', 'schools.id = users.school_id', 'left')
                                                      ->find($user['id']);
                return view('dashboard/scholar', $data);

            default:
                return redirect()->to('logout');
        }
    }
}
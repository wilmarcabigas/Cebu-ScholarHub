<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\SchoolModel;

class SuperAdminController extends BaseController
{
    protected $userModel;
    protected $schoolModel;

    public function __construct()
    {
        $this->userModel   = new UserModel();
        $this->schoolModel = new SchoolModel();
    }

    /* ==============================
       USER LIST (SUPERADMIN ONLY)
    ============================== */
public function index()
{
    $authUser = session()->get('auth_user');

    if (!$authUser || $authUser['role'] !== 'super_admin') {
        return redirect()->to('/')->with('error', 'Unauthorized access');
    }

    // Get filters
    $search   = $this->request->getGet('search');
    $role     = $this->request->getGet('role');
    $sort     = $this->request->getGet('sort') ?? 'desc';

    $builder = $this->userModel
        ->select('users.*, schools.name as school_name')
        ->join('schools', 'schools.id = users.school_id', 'left');

    // 🔍 SEARCH
    if ($search) {
        $builder->groupStart()
            ->like('email', $search)
            ->orLike('full_name', $search)
            ->groupEnd();
    }

    // 🎯 ROLE FILTER
    if ($role) {
        $builder->where('role', $role);
    }

    // 🔃 SORT BY CREATED DATE
    $builder->orderBy('created_at', $sort);

    $data = [
        'title'     => 'Manage Users',
        'users'     => $builder->findAll(),
        'show_back' => true,
        'back_url'  => site_url('dashboard'),
        'filters' => [
            'search' => $search,
            'role'   => $role,
            'sort'   => $sort
        ]
    ];

    return view('superadmin/user_list', $data);
}


    /* ==============================
       CREATE USER
    ============================== */
    public function create()
    {
        $authUser = session()->get('auth_user');

        if (!$authUser || $authUser['role'] !== 'super_admin') {
            return redirect()->to('/')->with('error', 'Unauthorized access');
        }

        $data = [
            'title'     => 'Create User',
            'schools'   => $this->schoolModel->findAll(),
            'show_back' => true,
            'back_url'  => site_url('dashboard'),
        ];

        if ($this->request->getMethod() === 'POST') {

            $role     = $this->request->getPost('role');
            $schoolId = $this->request->getPost('school_id');

            /* 🔐 BACKEND VALIDATION */
            if (in_array($role, ['school_admin', 'school_staff']) && empty($schoolId)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Assigned school is required for school users.');
            }

            $userData = [
                'email'      => $this->request->getPost('email'),
                'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                'full_name'  => $this->request->getPost('full_name'),
                'role'       => $role,
                'school_id'  => in_array($role, ['school_admin', 'school_staff']) ? $schoolId : null,
                'status'     => 'active',
                'created_at'=> date('Y-m-d H:i:s'),
            ];

            try {
                if ($this->userModel->insert($userData)) {
                    return redirect()->to('/superadmin/users')
                        ->with('message', 'User created successfully');
                }

                return redirect()->back()
                    ->withInput()
                    ->with('error', implode(', ', $this->userModel->errors()));

            } catch (\Exception $e) {
                log_message('error', '[User Create] ' . $e->getMessage());

                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Error creating user.');
            }
        }

        return view('superadmin/user_create', $data);
    }

    /* ==============================
       EDIT USER
    ============================== */
    public function edit($id = null)
    {
        $authUser = session()->get('auth_user');

        if (!$authUser || !in_array($authUser['role'], ['admin', 'super_admin'])) {
            return redirect()->to('/');
        }

        $user = $this->userModel->getUserById($id);

        if (!$user) {
            return redirect()->to('/superadmin/users')->with('error', 'User not found');
        }

        $data = [
            'title'     => 'Edit User',
            'user'      => $user,
            'schools'   => $this->schoolModel->findAll(),
            'show_back' => true,
            'back_url'  => site_url('superadmin/users'),
        ];

        if ($this->request->getMethod() === 'POST') {

            $role     = $this->request->getPost('role');
            $schoolId = $this->request->getPost('school_id');

            if (in_array($role, ['school_admin', 'school_staff']) && empty($schoolId)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Assigned school is required for school users.');
            }

            $updateData = [
                'email'     => $this->request->getPost('email'),
                'full_name' => $this->request->getPost('full_name'),
                'role'      => $role,
                'status'    => $this->request->getPost('status'),
                'school_id' => in_array($role, ['school_admin', 'school_staff']) ? $schoolId : null,
            ];

            if ($password = $this->request->getPost('password')) {
                $updateData['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
            }

            try {
                if ($this->userModel->update($id, $updateData)) {
                    return redirect()->to('/superadmin/users')
                        ->with('message', 'User updated successfully');
                }

                return redirect()->back()
                    ->withInput()
                    ->with('error', implode(', ', $this->userModel->errors()));

            } catch (\Exception $e) {
                log_message('error', '[User Edit] ' . $e->getMessage());

                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Error updating user.');
            }
        }

        return view('superadmin/user_edit', $data);
    }

    /* ==============================
       DELETE USER
    ============================== */
    public function delete($id)
    {
        $authUser = session()->get('auth_user');

        if (!$authUser || $authUser['role'] !== 'admin') {
            return redirect()->to('/');
        }

        $this->userModel->delete($id);

        return redirect()->to('/superadmin/users')
            ->with('message', 'User deleted successfully');
    }

    /* ==============================
       DEBUG ROLE
    ============================== */
    public function checkRole()
    {
        $sessionData = [
            'auth_user' => session()->get('auth_user'),
            'all'       => session()->get(),
        ];

        log_message('debug', 'Session Debug: ' . json_encode($sessionData));
        dd($sessionData);
    }

    public function dashboard()
{
    $db = \Config\Database::connect();

    // Stats (already existing)
    $stats = [
        'total_scholars' => $db->table('scholars')->countAll(),
        'active_schools' => $db->table('schools')->where('status', 'active')->countAllResults(),
        'pending_bills'  => $db->table('users')->countAll(),
    ];

    // Recent Users
    $recentUsers = $db->table('users')
        ->select('name, created_at')
        ->orderBy('created_at', 'DESC')
        ->limit(5)
        ->get()
        ->getResult();

    // Recent Schools
    $recentSchools = $db->table('schools')
        ->select('school_name, created_at')
        ->orderBy('created_at', 'DESC')
        ->limit(5)
        ->get()
        ->getResult();

    // Combine activities
    $activities = [];

    foreach ($recentUsers as $user) {
        $activities[] = [
            'type' => 'user',
            'message' => 'New user added',
            'name' => $user->name,
            'time' => $user->created_at
        ];
    }

    foreach ($recentSchools as $school) {
        $activities[] = [
            'type' => 'school',
            'message' => 'New school added',
            'name' => $school->school_name,
            'time' => $school->created_at
        ];
    }

    // Sort by latest
    usort($activities, function ($a, $b) {
        return strtotime($b['time']) - strtotime($a['time']);
    });

    // Limit to 5
    $activities = array_slice($activities, 0, 5);

    return view('superadmin/dashboard', [
        'stats' => $stats,
        'activities' => $activities
    ]);
}
}

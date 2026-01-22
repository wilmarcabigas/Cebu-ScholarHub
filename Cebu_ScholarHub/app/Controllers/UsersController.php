<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\SchoolModel;

class UsersController extends BaseController
{
    protected $userModel;
    protected $schoolModel;

    public function __construct()
    {
        $this->userModel   = new UserModel();
        $this->schoolModel = new SchoolModel();
    }

    /* ==============================
       USER LIST (ADMIN ONLY)
    ============================== */
    public function index()
    {
        $authUser = session()->get('auth_user');

        if (!$authUser || $authUser['role'] !== 'admin') {
            return redirect()->to('/')->with('error', 'Unauthorized access');
        }

        $data = [
            'title'     => 'Manage Users',
            'users'     => $this->userModel->getUsersWithSchool(),
            'show_back' => true,
            'back_url'  => site_url('dashboard'),
        ];

        return view('admin/user_list', $data);
    }

    /* ==============================
       CREATE USER
    ============================== */
    public function create()
    {
        $authUser = session()->get('auth_user');

        if (!$authUser || $authUser['role'] !== 'admin') {
            return redirect()->to('/')->with('error', 'Unauthorized access');
        }

        $data = [
            'title'     => 'Create User',
            'schools'   => $this->schoolModel->findAll(),
            'show_back' => true,
            'back_url'  => site_url('admin/users'),
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
                    return redirect()->to('/admin/users')
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

        return view('admin/user_create', $data);
    }

    /* ==============================
       EDIT USER
    ============================== */
    public function edit($id = null)
    {
        $authUser = session()->get('auth_user');

        if (!$authUser || $authUser['role'] !== 'admin') {
            return redirect()->to('/');
        }

        $user = $this->userModel->getUserById($id);

        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User not found');
        }

        $data = [
            'title'     => 'Edit User',
            'user'      => $user,
            'schools'   => $this->schoolModel->findAll(),
            'show_back' => true,
            'back_url'  => site_url('admin/users'),
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
                    return redirect()->to('/admin/users')
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

        return view('admin/user_edit', $data);
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

        return redirect()->to('/admin/users')
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
}

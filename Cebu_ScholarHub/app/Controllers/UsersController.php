<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
use App\Models\SchoolModel;
use CodeIgniter\Controller;
class UsersController extends BaseController
{
     protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // Admin Dashboard
    public function index()
{
    // Get auth user data from session
    $authUser = session()->get('auth_user');
    $currentRole = $authUser['role'] ?? null;
    
    log_message('debug', 'Current user role: ' . ($currentRole ?? 'null'));
    
    // Check if logged-in user is an admin
    if (!$currentRole || $currentRole !== 'admin') {
        log_message('debug', 'Access denied for role: ' . ($currentRole ?? 'null'));
        return redirect()->to('/');
    }

    // Get all users with school info
    $data['users'] = $this->userModel->getUsersWithSchool();
    return view('admin/user_list', $data);
}

    // Create User
      
   public function create()
{
    // Check authorization
    $authUser = session()->get('auth_user');
    if (!$authUser || $authUser['role'] !== 'admin') {
        return redirect()->to('/')->with('error', 'Unauthorized access');
    }

    // Get schools for dropdown
    $schoolModel = new \App\Models\SchoolModel();
    $data['schools'] = $schoolModel->findAll();

    if ($this->request->getMethod() === 'POST') {
        // Prepare user data
        $userData = [
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'full_name' => $this->request->getPost('full_name'),
            'role' => $this->request->getPost('role'),
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Add school_id if applicable
        if (in_array($userData['role'], ['school_admin', 'school_staff'])) {
            $userData['school_id'] = $this->request->getPost('school_id');
        }

        try {
            // Debug log
            log_message('debug', 'Attempting to create user with data: ' . json_encode($userData));

            if ($this->userModel->save($userData)) {
                return redirect()->to('/admin/users')
                    ->with('message', 'User created successfully');
            }

            return redirect()->back()
                ->with('error', implode(', ', $this->userModel->errors()))
                ->withInput();

        } catch (\Exception $e) {
            log_message('error', '[User Create] ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error creating user: ' . $e->getMessage())
                ->withInput();
        }
    }

    return view('admin/user_create', $data);
}
    // Edit User
    public function edit($id = null)
{
    // Check authorization
    $authUser = session()->get('auth_user');
    if (!$authUser || $authUser['role'] !== 'admin') {
        return redirect()->to('/');
    }

    // Get user and school data
    $user = $this->userModel->getUserById($id);
    
    if (!$user) {
        return redirect()->to('/admin/users')
            ->with('error', 'User not found');
    }

    // Get schools for dropdown if needed
    $schoolModel = new \App\Models\SchoolModel();
    $data = [
        'title' => 'Edit User',
        'user' => $user,
        'schools' => $schoolModel->findAll()
    ];

    if ($this->request->getMethod() === 'POST') {
        // Prepare update data
        $updateData = [
            'email' => $this->request->getPost('email'),
            'full_name' => $this->request->getPost('full_name'),
            'role' => $this->request->getPost('role'),
            'status' => $this->request->getPost('status'),
            'school_id' => $this->request->getPost('school_id')
        ];

        // Only update password if provided
        if ($password = $this->request->getPost('password')) {
            $updateData['password'] = $password;
        }

        try {
            if ($this->userModel->update($id, $updateData)) {
                return redirect()->to('/admin/users')
                    ->with('message', 'User updated successfully');
            }

            return redirect()->back()
                ->with('error', implode(', ', $this->userModel->errors()))
                ->withInput();

        } catch (\Exception $e) {
            log_message('error', '[User Edit] ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error updating user')
                ->withInput();
        }
    }

    return view('admin/user_edit', $data);
}
    // Delete User
    public function delete($id)
    {
       $authUser = session()->get('auth_user');
    if (!$authUser || $authUser['role'] !== 'admin') {
        return redirect()->to('/');
    }

        // Delete user by id
        $this->userModel->delete($id);
        return redirect()->to('/admin/users')->with('message', 'User deleted successfully!');
    }

  
public function checkRole()
{
    // Add more detailed session debugging
    $sessionData = [
        'role' => session()->get('role'),
        'user_id' => session()->get('user_id'),
        'auth_user' => session()->get('auth_user'),
        'logged_in' => session()->get('logged_in'),
        'all_session_data' => session()->get()
    ];
    
    // Log the session data
    log_message('debug', 'Session Data: ' . json_encode($sessionData));
    
    // Display the data
    dd($sessionData);
}
}
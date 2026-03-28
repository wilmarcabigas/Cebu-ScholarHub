<?php

namespace App\Controllers;

use App\Models\UserModel;

class ProfileController extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function edit()
    {
        $authUser = session()->get('auth_user');

        if (!$authUser) {
            return redirect()->to('login');
        }

        $user = $this->userModel->find($authUser['id']);

        if (!$user) {
            return redirect()->to('dashboard')->with('error', 'User not found.');
        }

        $data = [
            'title'     => 'Edit Profile',
            'user'      => $user,
            'show_back' => true,
            'back_url'  => site_url('dashboard'),
        ];

        return view('profile/edit', $data);
    }

    public function update()
    {
        $authUser = session()->get('auth_user');

        if (!$authUser) {
            return redirect()->to('login');
        }

        $userId   = $authUser['id'];
        $fullName = trim($this->request->getPost('full_name'));
        $email    = trim($this->request->getPost('email'));

        if (empty($fullName) || strlen($fullName) < 2) {
            return redirect()->back()->withInput()->with('error', 'Full name must be at least 2 characters.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->withInput()->with('error', 'Please enter a valid email address.');
        }

        // Check email uniqueness (exclude current user)
        $existing = $this->userModel->where('email', $email)->where('id !=', $userId)->first();
        if ($existing) {
            return redirect()->back()->withInput()->with('error', 'That email is already in use by another account.');
        }

        $updateData = [
            'full_name' => $fullName,
            'email'     => $email,
        ];

        // Password change (optional)
        $currentPassword = $this->request->getPost('current_password');
        $newPassword     = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');

        if (!empty($currentPassword) || !empty($newPassword) || !empty($confirmPassword)) {
            // All three fields must be filled
            if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                return redirect()->back()->withInput()->with('error', 'To change your password, fill in all three password fields.');
            }

            $user = $this->userModel->find($userId);

            if (!password_verify($currentPassword, $user['password_hash'])) {
                return redirect()->back()->withInput()->with('error', 'Current password is incorrect.');
            }

            if ($newPassword !== $confirmPassword) {
                return redirect()->back()->withInput()->with('error', 'New password and confirmation do not match.');
            }

            if (strlen($newPassword) < 8) {
                return redirect()->back()->withInput()->with('error', 'New password must be at least 8 characters.');
            }

            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).+$/', $newPassword)) {
                return redirect()->back()->withInput()->with('error', 'New password must contain uppercase, lowercase, number, and special character.');
            }

            $updateData['password_hash'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        try {
            $db = \Config\Database::connect();

            // Build raw SET clause to bypass model validation entirely
            $setClauses = [
                'full_name'  => $fullName,
                'email'      => $email,
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            if (isset($updateData['password_hash'])) {
                $setClauses['password_hash'] = $updateData['password_hash'];
            }

            $db->table('users')->where('id', $userId)->update($setClauses);

            // Refresh session name/email
            $updatedSession              = $authUser;
            $updatedSession['full_name'] = $fullName;
            $updatedSession['email']     = $email;
            session()->set('auth_user', $updatedSession);

            log_audit($userId, 'profile_update', 'users', $userId, 'User updated own profile');

            return redirect()->to('profile/edit')->with('success', 'Profile updated successfully.');
        } catch (\Exception $e) {
            log_message('error', '[ProfileController::update] ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'An error occurred while updating your profile.');
        }
    }
}

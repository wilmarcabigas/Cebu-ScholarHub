<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\Config\Services;
use CodeIgniter\I18n\Time;

class AuthController extends BaseController
{
    public function login()
    {
        return view('auth/login');
    }

    public function attempt()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please enter a valid email and password.');
        }

        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $users = new UserModel();

        // 🔍 Find active user
        $user = $users->findActiveByEmail($email);

        if (! $user) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Account not found or inactive.');
        }

        // 🔐 Verify password
        if (! password_verify($password, $user['password_hash'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Invalid email or password.');
        }

        // 🔄 Regenerate session
        $session = Services::session();
        $session->regenerate(true);

        // ✅ Store authenticated user
        $session->set('auth_user', [
            'id'        => $user['id'],
            'email'     => $user['email'],
            'full_name' => $user['full_name'],
            'role'      => $user['role'],
            'school_id' => $user['school_id'],
        ]);

        // 🕒 Update last login
        $users->update($user['id'], [
            'last_login_at' => Time::now()
        ]);

        return redirect()->to('/dashboard');
    }

    public function logout()
    {
        $session = Services::session();
        $session->destroy();

        return redirect()->to('/login')->with('message', 'Logged out successfully.');
    }
}

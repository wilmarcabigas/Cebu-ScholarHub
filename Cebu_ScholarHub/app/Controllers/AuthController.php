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
            'password' => 'required|min_length[6]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Invalid credentials.');
        }

        $email    = (string) $this->request->getPost('email');
        $password = (string) $this->request->getPost('password');

        $users = new UserModel();
        $user  = $users->findActiveByEmail($email);

        if (! $user || ! password_verify($password, $user['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'Invalid email or password.');
        }

        // Good practice: regenerate session id on login
        $session = Services::session();
        $session->regenerate(true);

        if ($user) {
            $session->set('auth_user', [
                'id' => $user['id'],
                'email' => $user['email'],
                'full_name' => $user['full_name'],
                'role' => $user['role'],
                'school_id' => $user['school_id'] ?? null
            ]);

            // Record last login
            $users->update($user['id'], ['last_login_at' => Time::now()]);

            // Redirect to dashboard (will handle role-specific routing)
            return redirect()->to('dashboard');
        }
    }

    public function logout()
    {
        $session = Services::session();
        $session->remove('auth_user');
        $session->destroy();
        return redirect()->to(site_url('login'))->with('message', 'Logged out.');
    }
}

<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\ActivityLogger;
use App\Models\UserModel;
use CodeIgniter\Config\Services;
use CodeIgniter\I18n\Time;

class AuthController extends BaseController
{
    protected $userModel;
    protected ActivityLogger $activityLogger;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->activityLogger = new ActivityLogger();
        helper(['form']);
    }

    // Show login form
    public function login()
    {
        return view('auth/login');
    }

    // STEP 1: Email + Password
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

        $email    = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');

        $user = $this->userModel->findActiveByEmail($email);

        if (! $user) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Account not found or inactive.');
        }

        // Keep 3-times lock account
        if (! empty($user['failed_attempts']) && (int) $user['failed_attempts'] >= 3) {
            return redirect()->to('/login')
                ->with('unlock_mode', true)
                ->with('locked_email', $user['email'])
                ->with('error', 'Account locked. Enter the unlock code sent to Gmail.');
        }

        // Check password
        if (! password_verify($password, $user['password_hash'])) {
            $attempts = ((int) ($user['failed_attempts'] ?? 0)) + 1;

            if ($attempts >= 3) {
                $unlockCode = (string) random_int(100000, 999999);

                $this->userModel->update((int) $user['id'], [
                    'failed_attempts' => $attempts,
                    'unlock_code'     => $unlockCode,
                ]);

                $this->sendUnlockCode($user['email'], $unlockCode);

                return redirect()->to('/login')
                    ->with('unlock_mode', true)
                    ->with('locked_email', $user['email'])
                    ->with('error', 'Account locked. Unlock code sent to Gmail.');
            }

            $this->userModel->update((int) $user['id'], [
                'failed_attempts' => $attempts,
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Invalid email or password.');
        }

        // Password correct → reset failed attempts
        $this->userModel->update((int) $user['id'], [
            'failed_attempts' => 0,
            'unlock_code'     => null,
        ]);

        // Send login verification code
        $loginCode = (string) random_int(100000, 999999);
        $expiresAt = Time::now()->addMinutes(10)->toDateTimeString();

        $this->userModel->update((int) $user['id'], [
            'login_code'            => $loginCode,
            'login_code_expires_at' => $expiresAt,
        ]);

        $session = Services::session();
        $session->set('pending_login_user_id', $user['id']);
        $session->set('pending_login_email', $user['email']);
        $session->set('pending_login_verified_password', true);

        $this->sendLoginCode($user['email'], $loginCode);

        return redirect()->to('/login')
            ->with('verify_mode', true)
            ->with('success', 'Verification code sent to your Gmail.');
    }

    // Cancel pending login and return to the login form
    public function cancelLogin()
    {
        $session = Services::session();
        $session->remove('pending_login_user_id');
        $session->remove('pending_login_email');
        $session->remove('pending_login_verified_password');

        return redirect()->to('/login');
    }

    // STEP 2: Verify login code
    public function verifyCode()
    {
        $session = Services::session();

        $pendingUserId = $session->get('pending_login_user_id');
        $passwordOk    = $session->get('pending_login_verified_password');

        if (! $pendingUserId || ! $passwordOk) {
            return redirect()->to('/login')
                ->with('error', 'Login session expired. Please login again.');
        }

        $code = trim((string) $this->request->getPost('code'));

        if ($code === '') {
            return redirect()->to('/login')
                ->with('verify_mode', true)
                ->with('error', 'Please enter the verification code.');
        }

        if (! preg_match('/^\d{6}$/', $code)) {
            return redirect()->to('/login')
                ->with('verify_mode', true)
                ->with('error', 'Verification code must be 6 digits.');
        }

        $user = $this->userModel->find((int) $pendingUserId);

        if (! $user) {
            $session->remove([
                'pending_login_user_id',
                'pending_login_email',
                'pending_login_verified_password',
            ]);

            return redirect()->to('/login')
                ->with('error', 'Account not found.');
        }

        if (
            empty($user['login_code']) ||
            empty($user['login_code_expires_at']) ||
            $user['login_code'] !== $code ||
            strtotime($user['login_code_expires_at']) < time()
        ) {
            return redirect()->to('/login')
                ->with('verify_mode', true)
                ->with('error', 'Invalid or expired verification code.');
        }

        // Clear login code
        $this->userModel->update((int) $user['id'], [
            'login_code'            => null,
            'login_code_expires_at' => null,
            'last_login_at'         => Time::now()->toDateTimeString(),
        ]);

        // Start real login session
        $session->regenerate(true);
        $session->set('auth_user', [
            'id'        => $user['id'],
            'email'     => $user['email'],
            'full_name' => $user['full_name'],
            'role'      => $user['role'],
            'school_id' => $user['school_id'],
        ]);

        // Remove temporary and reset-password session
        $session->remove([
            'pending_login_user_id',
            'pending_login_email',
            'pending_login_verified_password',
            'reset_password_user_id',
            'reset_password_email',
            'reset_password_verified',
        ]);

        $this->activityLogger->logSchoolAccountAction(
            $user,
            'account_login',
            'School account signed in',
            "{$user['full_name']} signed in to the partner school account.",
            [
                'action' => 'login',
                'subject_type' => 'user',
                'subject_id' => (int) $user['id'],
                'school_id' => $user['school_id'] ?? null,
                'new_values' => [
                    'last_login_at' => Time::now()->toDateTimeString(),
                ],
                'metadata' => [
                    'email' => $user['email'] ?? null,
                    'role' => $user['role'] ?? null,
                ],
            ]
        );

        return redirect()->to('/dashboard');
    }

    // Unlock account after 3 failed attempts
    // After valid unlock code, show reset-password form inside login page
    public function unlock()
    {
        $email = trim((string) $this->request->getPost('email'));
        $code  = trim((string) $this->request->getPost('unlock_code'));

        if ($email === '' || $code === '') {
            return redirect()->to('/login')
                ->with('unlock_mode', true)
                ->with('locked_email', $email)
                ->with('error', 'Please enter your email and unlock code.');
        }

        if (! preg_match('/^\d{6}$/', $code)) {
            return redirect()->to('/login')
                ->with('unlock_mode', true)
                ->with('locked_email', $email)
                ->with('error', 'Unlock code must be 6 digits.');
        }

        $user = $this->userModel->findActiveByEmail($email);

        if (! $user) {
            return redirect()->to('/login')
                ->with('error', 'Account not found or inactive.');
        }

        if ((int) ($user['failed_attempts'] ?? 0) < 3) {
            return redirect()->to('/login')
                ->with('error', 'This account is not locked.');
        }

        if ($user['unlock_code'] !== $code) {
            return redirect()->to('/login')
                ->with('unlock_mode', true)
                ->with('locked_email', $email)
                ->with('error', 'Invalid unlock code.');
        }

        $session = Services::session();
        $session->remove([
            'pending_login_user_id',
            'pending_login_email',
            'pending_login_verified_password',
        ]);
        $session->set('reset_password_user_id', $user['id']);
        $session->set('reset_password_email', $user['email']);
        $session->set('reset_password_verified', true);

        return redirect()->to('/login')
            ->with('success', 'Unlock code verified. Create your new password.');
    }

    // Save new password after unlock code verification
    public function processResetPassword()
    {
        $session = Services::session();

        $userId   = $session->get('reset_password_user_id');
        $verified = $session->get('reset_password_verified');

        if (! $userId || ! $verified) {
            return redirect()->to('/login')
                ->with('error', 'Reset password session expired. Please unlock again.');
        }

        $rules = [
            'password'         => 'required|min_length[8]',
            'confirm_password' => 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('/login')
                ->withInput()
                ->with('error', 'Password must be at least 8 characters and confirmation must match.');
        }

        $password = (string) $this->request->getPost('password');

        $this->userModel->update((int) $userId, [
            'password_hash'         => password_hash($password, PASSWORD_DEFAULT),
            'failed_attempts'       => 0,
            'unlock_code'           => null,
            'login_code'            => null,
            'login_code_expires_at' => null,
        ]);

        $session->remove([
            'reset_password_user_id',
            'reset_password_email',
            'reset_password_verified',
            'pending_login_user_id',
            'pending_login_email',
            'pending_login_verified_password',
        ]);

        return redirect()->to('/login')
            ->with('message', 'Password reset successful. Please login with your new password.');
    }

    // Resend login verification code
    public function resendCode()
    {
        $session = Services::session();
        $pendingUserId = $session->get('pending_login_user_id');

        if (! $pendingUserId) {
            return redirect()->to('/login')
                ->with('error', 'No pending login found. Please login again.');
        }

        $user = $this->userModel->find((int) $pendingUserId);

        if (! $user) {
            return redirect()->to('/login')
                ->with('error', 'Account not found.');
        }

        $loginCode = (string) random_int(100000, 999999);
        $expiresAt = Time::now()->addMinutes(10)->toDateTimeString();

        $this->userModel->update((int) $user['id'], [
            'login_code'            => $loginCode,
            'login_code_expires_at' => $expiresAt,
        ]);

        $this->sendLoginCode($user['email'], $loginCode);

        return redirect()->to('/login')
            ->with('verify_mode', true)
            ->with('success', 'A new verification code was sent to your Gmail.');
    }

    public function logout()
    {
        $session = Services::session();

        $user = $session->get('auth_user');
        if (is_array($user)) {
            $this->activityLogger->logSchoolAccountAction(
                $user,
                'account_logout',
                'School account signed out',
                "{$user['full_name']} signed out of the partner school account.",
                [
                    'action' => 'logout',
                    'subject_type' => 'user',
                    'subject_id' => (int) ($user['id'] ?? 0),
                    'school_id' => $user['school_id'] ?? null,
                    'metadata' => [
                        'email' => $user['email'] ?? null,
                        'role' => $user['role'] ?? null,
                    ],
                ]
            );
        }

        $session->destroy();

        return redirect()->to('/login')
            ->with('message', 'Logged out successfully.');
    }

    private function sendUnlockCode(string $email, string $code): void
    {
        $emailService = \Config\Services::email();

        $emailService->setTo($email);
        $emailService->setSubject('Unlock Your Account');
        $emailService->setMessage(
            'Your account has been locked because of 3 failed login attempts.<br><br>' .
            'Your unlock code is: <b>' . $code . '</b><br><br>' .
            'After entering this code, create a new password to unlock your account.'
        );

        $emailService->send();
    }

    private function sendLoginCode(string $email, string $code): void
    {
        $emailService = \Config\Services::email();

        $emailService->setTo($email);
        $emailService->setSubject('Your Login Verification Code');
        $emailService->setMessage(
            'Your login verification code is: <b>' . $code . '</b><br><br>' .
            'This code will expire in 10 minutes.'
        );

        $emailService->send();
    }
}

<?php
namespace App\Controllers;
use App\Models\UserModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    public function login()
    {
        if ($this->request->getMethod() === 'post') {
            
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');
             
            // Load the UserModel
            $userModel = new \App\Models\UserModel();
            
            // Attempt to find the user
            $user = $userModel->where('username', $username)->first();
            log_message('debug', 'Login attempt: ' . $username);
            if ($user && password_verify($password, $user['password'])) {
                // Set session data
                $session = session();
                $session->set([
                    'user_id' => $user['id'],
                    'username' => $user['username'],
                    'role' => $user['role'],
                    'logged_in' => true
                ]);
                
                // Redirect based on role
                if ($user['role'] === 'scholar_admin') {
                    return redirect()->to('/scholars');
                } else {
                    return redirect()->to('/school');
                }
            }
            
            // If login fails
            return redirect()->back()->with('error', 'Invalid username or password');
        }
        
        // Show login form
        return view('auth/login');
    }
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
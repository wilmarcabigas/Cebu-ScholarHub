<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\Config\Services;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = Services::session();
        $user    = $session->get('auth_user');

        if (! is_array($user)) {
            // Save intended URL so we can redirect after login
            $session->set('intended_url', current_url());
            return redirect()->to(site_url('login'));
        }
        return null; // allow
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // nothing
    }
}

<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Config\Services;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $roles = $arguments ?? [];
        $session = Services::session();
        $user = $session->get('auth_user');

        if (! is_array($user)) {
            $session->set('intended_url', current_url());
            return redirect()->to(site_url('login'));
        }

        $role = $user['role'] ?? null;
        if (! $role || (! empty($roles) && ! in_array($role, $roles, true))) {
            // 403 for unauthorized role
            return Services::response()
                ->setStatusCode(403)
                ->setBody(view('errors/html/error_403_custom'));
        }

        return null; // allow
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // nothing
    }
}

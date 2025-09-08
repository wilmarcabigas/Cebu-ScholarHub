<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $user = auth_user();

        // Simple role-based landing (you’ll replace with real dashboards later)
        if (! $user) {
            return redirect()->to(site_url('login'));
        }

        return view('dashboard/index', [
            'user' => $user,
        ]);
    }
}

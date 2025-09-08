<?php

use CodeIgniter\Config\Services;

if (! function_exists('auth_user')) {
    function auth_user(): ?array
    {
        $session = Services::session();
        $user = $session->get('auth_user');
        return is_array($user) ? $user : null;
    }
}

if (! function_exists('auth_id')) {
    function auth_id(): ?int
    {
        $u = auth_user();
        return $u['id'] ?? null;
    }
}

if (! function_exists('auth_role')) {
    function auth_role(): ?string
    {
        $u = auth_user();
        return $u['role'] ?? null;
    }
}

if (! function_exists('auth_has_role')) {
    function auth_has_role(string|array $roles): bool
    {
        $role = auth_role();
        $roles = (array) $roles;
        return $role && in_array($role, $roles, true);
    }
}

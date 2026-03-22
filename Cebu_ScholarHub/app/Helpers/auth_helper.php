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

if (! function_exists('auth_school_id')) {
    function auth_school_id(): ?int
    {
        $u = auth_user();
        return $u['school_id'] ?? null;
    }
}

if (! function_exists('log_audit')) {
    function log_audit(int $userId, string $actionType, string $tableName, int $recordId, string $details): void
    {
        try {
            db_connect()->table('audit_logs')->insert([
                'user_id'        => $userId,
                'action_type'    => $actionType,
                'table_name'     => $tableName,
                'record_id'      => $recordId,
                'action_details' => $details,
                'action_time'    => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            // Never let audit logging break the main flow
            log_message('error', 'audit_log failed: ' . $e->getMessage());
        }
    }
}

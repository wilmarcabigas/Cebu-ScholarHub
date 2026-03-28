<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table = 'audit_logs';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'activity_log_id',
        'actor_user_id',
        'school_id',
        'event_type',
        'action',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'metadata',
        'request_method',
        'request_path',
        'ip_address',
        'created_at',
    ];

    public function getRecentWithRelations(array $filters = [], int $limit = 50): array
    {
        $builder = $this->select('
                audit_logs.*,
                users.full_name AS actor_name,
                users.email AS actor_email,
                users.role AS actor_role,
                schools.name AS school_name,
                activity_logs.title AS activity_title
            ')
            ->join('users', 'users.id = audit_logs.actor_user_id', 'left')
            ->join('schools', 'schools.id = audit_logs.school_id', 'left')
            ->join('activity_logs', 'activity_logs.id = audit_logs.activity_log_id', 'left')
            ->orderBy('audit_logs.created_at', 'DESC');

        if (! empty($filters['school_id'])) {
            $builder->where('audit_logs.school_id', (int) $filters['school_id']);
        }

        if (! empty($filters['event_type'])) {
            $builder->where('audit_logs.event_type', $filters['event_type']);
        }

        if (! empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            $builder->groupStart()
                ->like('audit_logs.event_type', $search)
                ->orLike('audit_logs.action', $search)
                ->orLike('users.full_name', $search)
                ->orLike('schools.name', $search)
                ->orLike('activity_logs.title', $search)
                ->groupEnd();
        }

        return $builder->findAll($limit);
    }
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table = 'activity_logs';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'actor_user_id',
        'school_id',
        'event_type',
        'title',
        'description',
        'subject_type',
        'subject_id',
        'request_method',
        'request_path',
        'ip_address',
        'user_agent',
        'metadata',
        'created_at',
    ];

    public function getRecentWithRelations(array $filters = [], int $limit = 50): array
    {
        $builder = $this->select('
                activity_logs.*,
                users.full_name AS actor_name,
                users.email AS actor_email,
                users.role AS actor_role,
                schools.name AS school_name
            ')
            ->join('users', 'users.id = activity_logs.actor_user_id', 'left')
            ->join('schools', 'schools.id = activity_logs.school_id', 'left')
            ->orderBy('activity_logs.created_at', 'DESC');

        if (! empty($filters['school_id'])) {
            $builder->where('activity_logs.school_id', (int) $filters['school_id']);
        }

        if (! empty($filters['event_type'])) {
            $builder->where('activity_logs.event_type', $filters['event_type']);
        }

        if (! empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            $builder->groupStart()
                ->like('activity_logs.title', $search)
                ->orLike('activity_logs.description', $search)
                ->orLike('users.full_name', $search)
                ->orLike('schools.name', $search)
                ->groupEnd();
        }

        return $builder->findAll($limit);
    }
}

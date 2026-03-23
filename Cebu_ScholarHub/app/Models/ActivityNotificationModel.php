<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityNotificationModel extends Model
{
    protected $table = 'activity_notifications';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'recipient_user_id',
        'actor_user_id',
        'school_id',
        'event_type',
        'title',
        'message',
        'link',
        'is_read',
        'created_at',
    ];

    public function getRecentForRecipient(int $userId, int $limit = 8): array
    {
        return $this->where('recipient_user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll($limit);
    }

    public function countUnreadForRecipient(int $userId): int
    {
        return $this->where('recipient_user_id', $userId)
            ->where('is_read', 0)
            ->countAllResults();
    }

    public function markAllAsReadForRecipient(int $userId): bool
    {
        return (bool) $this->where('recipient_user_id', $userId)
            ->set(['is_read' => 1])
            ->update();
    }
}

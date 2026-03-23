<?php

namespace App\Libraries;

use App\Models\ActivityNotificationModel;
use App\Models\UserModel;

class ActivityNotifier
{
    protected ActivityNotificationModel $activityNotificationModel;
    protected UserModel $userModel;

    public function __construct()
    {
        $this->activityNotificationModel = new ActivityNotificationModel();
        $this->userModel = new UserModel();
    }

    public function notifySchoolActivity(
        array $actor,
        string $eventType,
        string $title,
        string $message,
        ?string $link = null,
        ?int $schoolId = null
    ): void {
        if (! in_array($actor['role'] ?? '', ['school_admin', 'school_staff'], true)) {
            return;
        }

        $recipientIds = $this->userModel
            ->whereIn('role', ['admin', 'staff'])
            ->where('status', 'active')
            ->findColumn('id');

        if (empty($recipientIds)) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $rows = [];

        foreach (array_unique($recipientIds) as $recipientId) {
            $rows[] = [
                'recipient_user_id' => (int) $recipientId,
                'actor_user_id' => (int) $actor['id'],
                'school_id' => $schoolId,
                'event_type' => $eventType,
                'title' => $title,
                'message' => $message,
                'link' => $link,
                'is_read' => 0,
                'created_at' => $now,
            ];
        }

        if ($rows !== []) {
            $this->activityNotificationModel->insertBatch($rows);
        }
    }
}

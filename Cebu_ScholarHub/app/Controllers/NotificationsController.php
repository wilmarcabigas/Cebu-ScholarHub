<?php

namespace App\Controllers;

use App\Models\ActivityNotificationModel;

class NotificationsController extends BaseController
{
    protected ActivityNotificationModel $activityNotificationModel;

    public function __construct()
    {
        $this->activityNotificationModel = new ActivityNotificationModel();
    }

    public function markAllRead()
    {
        $user = auth_user();

        if (! $user || ! in_array($user['role'] ?? '', ['admin', 'staff'], true)) {
            return redirect()->to(site_url('dashboard'))
                ->with('error', 'Unauthorized access.');
        }

        $this->activityNotificationModel->markAllAsReadForRecipient((int) $user['id']);

        return redirect()->back()
            ->with('message', 'Notifications marked as read.');
    }
}

<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ActivityLogModel;
use App\Models\AuditLogModel;
use App\Models\SchoolModel;

class ActivityLogs extends BaseController
{
    protected ActivityLogModel $activityLogModel;
    protected AuditLogModel $auditLogModel;
    protected SchoolModel $schoolModel;

    public function __construct()
    {
        $this->activityLogModel = new ActivityLogModel();
        $this->auditLogModel = new AuditLogModel();
        $this->schoolModel = new SchoolModel();
    }

    public function index()
    {
        $filters = [
            'search'     => trim((string) $this->request->getGet('search')),
            'school_id'  => $this->request->getGet('school_id'),
            'event_type' => trim((string) $this->request->getGet('event_type')),
        ];

        $activityLogs = $this->activityLogModel->getRecentWithRelations($filters, 100);
        $auditLogs = $this->auditLogModel->getRecentWithRelations($filters, 100);

        foreach ($activityLogs as &$log) {
            $log['metadata_array'] = $this->decodeJsonField($log['metadata'] ?? null);
        }
        unset($log);

        foreach ($auditLogs as &$log) {
            $log['old_values_array'] = $this->decodeJsonField($log['old_values'] ?? null);
            $log['new_values_array'] = $this->decodeJsonField($log['new_values'] ?? null);
            $log['metadata_array'] = $this->decodeJsonField($log['metadata'] ?? null);
        }
        unset($log);

        return view('admin/logs/index', [
            'title'        => 'Activity and Audit Logs',
            'show_back'    => true,
            'back_url'     => site_url('dashboard'),
            'filters'      => $filters,
            'schools'      => $this->schoolModel->findAll(),
            'activityLogs' => $activityLogs,
            'auditLogs'    => $auditLogs,
        ]);
    }

    protected function decodeJsonField(?string $value): array
    {
        if (! $value) {
            return [];
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : [];
    }
}

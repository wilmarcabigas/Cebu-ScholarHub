<?php

namespace App\Libraries;

use App\Models\ActivityLogModel;
use App\Models\AuditLogModel;
use CodeIgniter\HTTP\IncomingRequest;
use Config\Services;

class ActivityLogger
{
    protected ActivityLogModel $activityLogModel;
    protected AuditLogModel $auditLogModel;
    protected ?IncomingRequest $request;

    public function __construct()
    {
        $this->activityLogModel = new ActivityLogModel();
        $this->auditLogModel = new AuditLogModel();

        $request = Services::request();
        $this->request = $request instanceof IncomingRequest ? $request : null;
    }

    public function logSchoolAccountAction(array $actor, string $eventType, string $title, string $description, array $options = []): ?int
    {
        if (! in_array($actor['role'] ?? '', ['school_admin', 'school_staff'], true)) {
            return null;
        }

        $requestMeta = $this->captureRequestMeta();
        $createdAt = date('Y-m-d H:i:s');
        $metadata = $options['metadata'] ?? [];
        $action = $options['action'] ?? 'update';
        $subjectType = $options['subject_type'] ?? 'record';
        $subjectId = (int) ($options['subject_id'] ?? 0);
        $encodedMetadata = $this->encodeJson($metadata);
        $encodedOldValues = $this->encodeJson($options['old_values'] ?? null);
        $encodedNewValues = $this->encodeJson($options['new_values'] ?? null);

        $activityData = [
            'actor_user_id'  => $actor['id'] ?? null,
            'school_id'      => $options['school_id'] ?? ($actor['school_id'] ?? null),
            'event_type'     => $eventType,
            'title'          => $title,
            'description'    => $description,
            'subject_type'   => $options['subject_type'] ?? null,
            'subject_id'     => $options['subject_id'] ?? null,
            'request_method' => $requestMeta['request_method'],
            'request_path'   => $requestMeta['request_path'],
            'ip_address'     => $requestMeta['ip_address'],
            'user_agent'     => $requestMeta['user_agent'],
            'metadata'       => $encodedMetadata,
            'created_at'     => $createdAt,
        ];

        $activityId = $this->activityLogModel->insert($activityData, true);

        $this->auditLogModel->insert([
            'activity_log_id' => $activityId ?: null,
            'actor_user_id'   => $actor['id'] ?? null,
            'school_id'       => $options['school_id'] ?? ($actor['school_id'] ?? null),
            'event_type'      => $eventType,
            'action'          => $action,
            'auditable_type'  => $options['subject_type'] ?? null,
            'auditable_id'    => $options['subject_id'] ?? null,
            'old_values'      => $encodedOldValues,
            'new_values'      => $encodedNewValues,
            'metadata'        => $encodedMetadata,
            'request_method'  => $requestMeta['request_method'],
            'request_path'    => $requestMeta['request_path'],
            'ip_address'      => $requestMeta['ip_address'],
            'created_at'      => $createdAt,
            'user_id'         => (int) ($actor['id'] ?? 0),
            'action_type'     => $eventType,
            'table_name'      => $subjectType,
            'record_id'       => $subjectId,
            'action_details'  => $encodedNewValues ?? $encodedMetadata ?? $description,
            'action_time'     => $createdAt,
        ]);

        return $activityId ?: null;
    }

    protected function captureRequestMeta(): array
    {
        if (! $this->request) {
            return [
                'request_method' => null,
                'request_path'   => null,
                'ip_address'     => null,
                'user_agent'     => null,
            ];
        }

        return [
            'request_method' => $this->request->getMethod(),
            'request_path'   => trim($this->request->getUri()->getPath(), '/'),
            'ip_address'     => $this->request->getIPAddress(),
            'user_agent'     => (string) $this->request->getUserAgent(),
        ];
    }

    protected function encodeJson($value): ?string
    {
        if ($value === null) {
            return null;
        }

        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}

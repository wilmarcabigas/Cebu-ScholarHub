<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpgradeLegacyAuditLogs extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('audit_logs')) {
            return;
        }

        $columns = [
            'activity_log_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id',
            ],
            'actor_user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'activity_log_id',
            ],
            'school_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'actor_user_id',
            ],
            'event_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'school_id',
            ],
            'action' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'event_type',
            ],
            'auditable_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'action',
            ],
            'auditable_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'auditable_type',
            ],
            'old_values' => [
                'type' => 'LONGTEXT',
                'null' => true,
                'after' => 'auditable_id',
            ],
            'new_values' => [
                'type' => 'LONGTEXT',
                'null' => true,
                'after' => 'old_values',
            ],
            'metadata' => [
                'type' => 'LONGTEXT',
                'null' => true,
                'after' => 'new_values',
            ],
            'request_method' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'after'      => 'metadata',
            ],
            'request_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'request_method',
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 45,
                'null'       => true,
                'after'      => 'request_path',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'ip_address',
            ],
        ];

        foreach ($columns as $name => $definition) {
            if (! $this->db->fieldExists($name, 'audit_logs')) {
                $this->forge->addColumn('audit_logs', [
                    $name => $definition,
                ]);
            }
        }

        $indexMap = [
            'activity_log_id',
            'actor_user_id',
            'school_id',
            'event_type',
            'action',
        ];

        foreach ($indexMap as $field) {
            try {
                $this->forge->addKey($field);
            } catch (\Throwable $e) {
            }
        }

        $this->db->query("
            UPDATE audit_logs
            SET actor_user_id = COALESCE(actor_user_id, user_id),
                event_type = COALESCE(event_type, action_type),
                action = COALESCE(action, action_type),
                auditable_type = COALESCE(auditable_type, table_name),
                auditable_id = COALESCE(auditable_id, record_id),
                metadata = COALESCE(metadata, action_details),
                created_at = COALESCE(created_at, action_time)
        ");
    }

    public function down()
    {
    }
}

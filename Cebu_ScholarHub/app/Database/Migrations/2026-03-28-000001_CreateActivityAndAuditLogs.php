<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateActivityAndAuditLogs extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('activity_logs')) {
            $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'actor_user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'school_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'event_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'subject_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'subject_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'request_method' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            'request_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 45,
                'null'       => true,
            ],
            'user_agent' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'metadata' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('actor_user_id');
            $this->forge->addKey('school_id');
            $this->forge->addKey('event_type');
            $this->forge->addKey('subject_type');
            $this->forge->createTable('activity_logs');
        }

        if (! $this->db->tableExists('audit_logs')) {
            $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'activity_log_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'actor_user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'school_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'event_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'action' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'auditable_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'auditable_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'old_values' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'new_values' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'metadata' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'request_method' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            'request_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 45,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('activity_log_id');
            $this->forge->addKey('actor_user_id');
            $this->forge->addKey('school_id');
            $this->forge->addKey('event_type');
            $this->forge->addKey('action');
            $this->forge->createTable('audit_logs');
        }
    }

    public function down()
    {
        $this->forge->dropTable('audit_logs', true);
        $this->forge->dropTable('activity_logs', true);
    }
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateActivityNotifications extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('activity_notifications')) {
            return;
        }

        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'recipient_user_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
            ],
            'actor_user_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
            ],
            'school_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'null' => true,
            ],
            'event_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'message' => [
                'type' => 'TEXT',
            ],
            'link' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'is_read' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('recipient_user_id');
        $this->forge->addKey('actor_user_id');
        $this->forge->addKey('school_id');
        $this->forge->addKey('event_type');
        $this->forge->addForeignKey('recipient_user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('actor_user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('school_id', 'schools', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('activity_notifications');
    }

    public function down()
    {
        if ($this->db->tableExists('activity_notifications')) {
            $this->forge->dropTable('activity_notifications', true);
        }
    }
}

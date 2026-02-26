<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJobsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'type' => ['type' => 'VARCHAR', 'constraint' => 100],
            'payload' => ['type' => 'JSONB', 'null' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pending'],
            'attempts' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'error_message' => ['type' => 'TEXT', 'null' => true],
            'scheduled_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'started_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'completed_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['type', 'status']);
        $this->forge->addKey('scheduled_at');
        $this->forge->createTable('jobs');
    }

    public function down(): void
    {
        $this->forge->dropTable('jobs');
    }
}
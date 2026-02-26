<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGmailWatchesTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'watch_id' => ['type' => 'VARCHAR', 'constraint' => 255],
            'history_id' => ['type' => 'VARCHAR', 'constraint' => 255],
            'topic_resource_id' => ['type' => 'VARCHAR', 'constraint' => 255],
            'expiration' => ['type' => 'TIMESTAMP', 'null' => true],
            'is_active' => ['type' => 'BOOLEAN', 'default' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('user_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('gmail_watches');
    }

    public function down(): void
    {
        $this->forge->dropTable('gmail_watches');
    }
}
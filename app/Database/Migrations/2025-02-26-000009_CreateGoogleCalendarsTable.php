<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGoogleCalendarsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'google_calendar_id' => ['type' => 'VARCHAR', 'constraint' => 255],
            'name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'is_primary' => ['type' => 'BOOLEAN', 'default' => false],
            'sync_enabled' => ['type' => 'BOOLEAN', 'default' => true],
            'board_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('board_id', 'boards', 'id', 'SET NULL', 'SET NULL');
        $this->forge->createTable('google_calendars');
    }

    public function down(): void
    {
        $this->forge->dropTable('google_calendars');
    }
}
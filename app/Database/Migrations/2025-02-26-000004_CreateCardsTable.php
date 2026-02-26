<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCardsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'column_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'title' => ['type' => 'VARCHAR', 'constraint' => 200],
            'description' => ['type' => 'TEXT', 'null' => true],
            'position' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'priority' => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => 'low'],
            'due_date' => ['type' => 'TIMESTAMP', 'null' => true],
            'is_completed' => ['type' => 'BOOLEAN', 'default' => false],
            'google_event_id' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('column_id', 'columns', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('cards');
    }

    public function down(): void
    {
        $this->forge->dropTable('cards');
    }
}
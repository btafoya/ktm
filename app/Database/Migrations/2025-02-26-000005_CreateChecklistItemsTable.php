<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateChecklistItemsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'card_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'title' => ['type' => 'VARCHAR', 'constraint' => 200],
            'is_completed' => ['type' => 'BOOLEAN', 'default' => false],
            'position' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('card_id', 'cards', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('checklist_items');
    }

    public function down(): void
    {
        $this->forge->dropTable('checklist_items');
    }
}
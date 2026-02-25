<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateChecklistItemsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'card_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'text' => [
                'type' => 'TEXT',
            ],
            'completed' => [
                'type'       => 'BOOLEAN',
                'default'    => false,
            ],
            'position' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('card_id');
        $this->forge->addForeignKey('card_id', 'cards', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('checklist_items');
    }

    public function down(): void
    {
        $this->forge->dropTable('checklist_items');
    }
}
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBoardsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'description' => ['type' => 'TEXT', 'null' => true],
            'is_public' => ['type' => 'BOOLEAN', 'default' => false],
            'is_default' => ['type' => 'BOOLEAN', 'default' => false],
            'background_color' => ['type' => 'VARCHAR', 'constraint' => 7, 'default' => '#212529'],
            'column_order' => ['type' => 'JSONB', 'null' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('boards');
    }

    public function down(): void
    {
        $this->forge->dropTable('boards');
    }
}
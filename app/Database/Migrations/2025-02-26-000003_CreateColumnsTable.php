<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateColumnsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'board_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'color' => ['type' => 'VARCHAR', 'constraint' => 7, 'default' => '#0d6efd'],
            'position' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('board_id', 'boards', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('columns');
    }

    public function down(): void
    {
        $this->forge->dropTable('columns');
    }
}
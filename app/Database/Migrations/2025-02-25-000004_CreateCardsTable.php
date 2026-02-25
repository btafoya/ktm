<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCardsTable extends Migration
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
            'column_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'board_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'color' => [
                'type'       => 'VARCHAR',
                'constraint' => 7,
                'default'    => '#6c757d',
            ],
            'priority' => [
                'type'       => 'ENUM',
                'constraint' => ['low', 'medium', 'high'],
                'default'    => 'medium',
            ],
            'due_date' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['task', 'calendar', 'email'],
                'default'    => 'task',
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
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('column_id');
        $this->forge->addKey('board_id');
        $this->forge->addKey('due_date');
        $this->forge->addKey('type');
        $this->forge->addForeignKey('column_id', 'columns', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('board_id', 'boards', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('cards');
    }

    public function down(): void
    {
        $this->forge->dropTable('cards');
    }
}
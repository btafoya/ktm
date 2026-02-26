<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGmailSendersTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'email' => ['type' => 'VARCHAR', 'constraint' => 255],
            'name' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'card_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'column_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'keyword' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'is_active' => ['type' => 'BOOLEAN', 'default' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('card_id', 'cards', 'id', 'SET NULL', 'SET NULL');
        $this->forge->addForeignKey('column_id', 'columns', 'id', 'SET NULL', 'SET NULL');
        $this->forge->createTable('gmail_senders');
    }

    public function down(): void
    {
        $this->forge->dropTable('gmail_senders');
    }
}
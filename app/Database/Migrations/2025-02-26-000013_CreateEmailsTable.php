<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmailsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'card_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'gmail_message_id' => ['type' => 'VARCHAR', 'constraint' => 255],
            'thread_id' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'sender_email' => ['type' => 'VARCHAR', 'constraint' => 255],
            'sender_name' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'subject' => ['type' => 'TEXT'],
            'snippet' => ['type' => 'TEXT', 'null' => true],
            'body' => ['type' => 'TEXT', 'null' => true],
            'received_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('card_id', 'cards', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('emails');
    }

    public function down(): void
    {
        $this->forge->dropTable('emails');
    }
}
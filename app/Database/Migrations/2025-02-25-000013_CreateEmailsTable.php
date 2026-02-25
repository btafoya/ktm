<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmailsTable extends Migration
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
            'thread_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'gmail_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'card_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'from_email' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'from_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'subject' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
            ],
            'body' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'snippet' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('thread_id');
        $this->forge->addKey('card_id');
        $this->forge->addUniqueKey('gmail_id');
        $this->forge->addForeignKey('card_id', 'cards', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('emails');
    }

    public function down(): void
    {
        $this->forge->dropTable('emails');
    }
}
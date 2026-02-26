<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAttachmentsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'card_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'file_name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'file_path' => ['type' => 'VARCHAR', 'constraint' => 500],
            'file_size' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'mime_type' => ['type' => 'VARCHAR', 'constraint' => 100],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('card_id', 'cards', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('attachments');
    }

    public function down(): void
    {
        $this->forge->dropTable('attachments');
    }
}
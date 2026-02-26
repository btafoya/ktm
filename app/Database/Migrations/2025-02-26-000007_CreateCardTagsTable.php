<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCardTagsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'card_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'tag_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('card_id', 'cards', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('tag_id', 'tags', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('card_tags');
    }

    public function down(): void
    {
        $this->forge->dropTable('card_tags');
    }
}
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCardTagsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'card_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tag_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
        ]);

        $this->forge->addPrimaryKey(['card_id', 'tag_id']);
        $this->forge->addKey('card_id');
        $this->forge->addKey('tag_id');
        $this->forge->addForeignKey('card_id', 'cards', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('tag_id', 'tags', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('card_tags');
    }

    public function down(): void
    {
        $this->forge->dropTable('card_tags');
    }
}
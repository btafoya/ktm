<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGmailWatchesTable extends Migration
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
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'watch_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'resource_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'history_id' => [
                'type' => 'BIGINT',
            ],
            'expiration' => [
                'type' => 'TIMESTAMP',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addUniqueKey('watch_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('gmail_watches');
    }

    public function down(): void
    {
        $this->forge->dropTable('gmail_watches');
    }
}
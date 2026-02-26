<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGoogleTokensTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'access_token' => ['type' => 'TEXT'],
            'refresh_token' => ['type' => 'TEXT'],
            'expires_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'scope' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('user_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('google_tokens');
    }

    public function down(): void
    {
        $this->forge->dropTable('google_tokens');
    }
}
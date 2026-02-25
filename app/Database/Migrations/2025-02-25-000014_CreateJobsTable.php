<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJobsTable extends Migration
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
            'queue' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'default',
            ],
            'payload' => [
                'type' => 'JSON',
            ],
            'attempts' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'reserved_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'available_at' => [
                'type' => 'TIMESTAMP',
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('queue');
        $this->forge->addKey('available_at');
        $this->forge->createTable('jobs');
    }

    public function down(): void
    {
        $this->forge->dropTable('jobs');
    }
}
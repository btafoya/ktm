<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCardBodyField extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('cards', [
            'body' => ['type' => 'TEXT', 'null' => true, 'after' => 'description'],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('cards', 'body');
    }
}
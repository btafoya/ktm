<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddExternalFieldsToCardsTable extends Migration
{
    public function up(): void
    {
        $fields = [
            'start_date' => ['type' => 'TIMESTAMP', 'null' => true, 'after' => 'position'],
            'is_calendar_event' => ['type' => 'BOOLEAN', 'default' => false, 'after' => 'is_completed'],
            'external_id' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'google_event_id'],
            'external_source' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'after' => 'external_id'],
            'external_data' => ['type' => 'TEXT', 'null' => true, 'after' => 'external_source'],
            'is_email' => ['type' => 'BOOLEAN', 'default' => false, 'after' => 'external_data'],
        ];

        $this->forge->addColumn('cards', $fields);
    }

    public function down(): void
    {
        $this->forge->dropColumn('cards', [
            'start_date',
            'is_calendar_event',
            'external_id',
            'external_source',
            'external_data',
            'is_email',
        ]);
    }
}
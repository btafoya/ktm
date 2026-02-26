<?php

namespace App\Models;

use CodeIgniter\Model;

class GoogleCalendarModel extends Model
{
    protected $table = 'google_calendars';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id', 'google_calendar_id', 'name',
        'is_primary', 'sync_enabled', 'board_id', 'created_at', 'updated_at'
    ];

    protected $useTimestamps = false;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getForUser(int $userId): array
    {
        return $this->where('user_id', $userId)
            ->orderBy('is_primary', 'DESC')
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    public function getSyncCalendars(int $userId): array
    {
        return $this->where('user_id', $userId)
            ->where('sync_enabled', true)
            ->findAll();
    }
}
<?php

namespace App\Models;

use CodeIgniter\Model;

class GoogleCalendarModel extends Model
{
    protected $table            = 'google_calendars';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'calendar_id',
        'name',
        'primary_calendar',
        'selected',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'user_id'        => 'required|integer',
        'calendar_id'    => 'required|string|max_length[255]',
        'name'           => 'required|string|max_length[255]',
        'primary_calendar' => 'permit_empty|boolean',
        'selected'       => 'permit_empty|boolean',
    ];
    protected $skipValidation = false;

    /**
     * Get calendars for a user
     */
    public function getForUser(int $userId): array
    {
        return $this->where('user_id', $userId)
            ->orderBy('primary_calendar', 'DESC')
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    /**
     * Get selected calendars for a user
     */
    public function getSelected(int $userId): array
    {
        return $this->where('user_id', $userId)
            ->where('selected', true)
            ->findAll();
    }

    /**
     * Find calendar by Google ID for user
     */
    public function findByGoogleId(int $userId, string $calendarId): ?array
    {
        return $this->where('user_id', $userId)
            ->where('calendar_id', $calendarId)
            ->first();
    }

    /**
     * Sync calendars from Google API
     */
    public function syncCalendars(int $userId, array $calendars): int
    {
        $synced = 0;

        foreach ($calendars as $calendar) {
            $existing = $this->findByGoogleId($userId, $calendar['id']);

            $data = [
                'user_id'          => $userId,
                'calendar_id'      => $calendar['id'],
                'name'             => $calendar['summary'],
                'primary_calendar' => $calendar['primary'] ?? false,
                'updated_at'       => date('Y-m-d H:i:s'),
            ];

            if ($existing) {
                $this->update($existing['id'], $data);
            } else {
                $this->insert($data);
            }

            $synced++;
        }

        return $synced;
    }

    /**
     * Toggle calendar selection
     */
    public function toggleSelection(int $id): bool
    {
        $calendar = $this->find($id);

        if (!$calendar) {
            return false;
        }

        return $this->update($id, ['selected' => !$calendar['selected']]);
    }
}
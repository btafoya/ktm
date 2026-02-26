<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\GoogleCalendarModel;
use App\Models\GoogleTokenModel;
use App\Models\BoardModel;
use App\Models\ColumnModel;
use App\Models\CardModel;

class CalendarSyncService
{
    private GoogleAuthService $authService;
    private GoogleCalendarModel $calendarModel;
    private GoogleTokenModel $tokenModel;
    private BoardModel $boardModel;
    private ColumnModel $columnModel;
    private CardModel $cardModel;

    public function __construct()
    {
        $this->authService = new GoogleAuthService();
        $this->calendarModel = new GoogleCalendarModel();
        $this->tokenModel = new GoogleTokenModel();
        $this->boardModel = new BoardModel();
        $this->columnModel = new ColumnModel();
        $this->cardModel = new CardModel();
    }

    public function fetchCalendars(int $userId): ?array
    {
        $token = $this->authService->getAccessToken($userId);

        if (!$token) {
            return null;
        }

        $calendarUrl = 'https://www.googleapis.com/calendar/v3/users/me/calendarList';

        $ch = curl_init($calendarUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer {$token}"]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 401) {
            $token = $this->authService->refreshAccessToken($userId);
            if (!$token) {
                return null;
            }

            $ch = curl_init($calendarUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer {$token}"]);

            $response = curl_exec($ch);
            curl_close($ch);
        }

        $data = json_decode($response, true);

        return $data['items'] ?? null;
    }

    public function syncCalendarEvents(int $userId, int $calendarId): array
    {
        $calendar = $this->calendarModel->find($calendarId);

        if (!$calendar || $calendar['user_id'] !== $userId || !$calendar['sync_enabled']) {
            return ['success' => false, 'message' => 'Invalid calendar.'];
        }

        $token = $this->authService->getAccessToken($userId);

        if (!$token) {
            return ['success' => false, 'message' => 'No valid token.'];
        }

        $timeMin = date('c', strtotime('today 00:00:00'));
        $timeMax = date('c', strtotime('+30 days 23:59:59'));

        $eventsUrl = "https://www.googleapis.com/calendar/v3/calendars/" .
            urlencode($calendar['google_calendar_id']) .
            "/events?timeMin={$timeMin}&timeMax={$timeMax}&singleEvents=true&orderBy=startTime";

        $ch = curl_init($eventsUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer {$token}"]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 401) {
            $token = $this->authService->refreshAccessToken($userId);
            if (!$token) {
                return ['success' => false, 'message' => 'Failed to refresh token.'];
            }

            $ch = curl_init($eventsUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer {$token}"]);

            $response = curl_exec($ch);
            curl_close($ch);
        }

        $data = json_decode($response, true);
        $events = $data['items'] ?? [];

        $created = 0;
        $updated = 0;

        foreach ($events as $event) {
            $result = $this->syncEvent($userId, $calendar, $event);
            if ($result === 'created') {
                $created++;
            } elseif ($result === 'updated') {
                $updated++;
            }
        }

        return [
            'success' => true,
            'message' => "Synced {$created} new events, updated {$updated} events.",
            'created' => $created,
            'updated' => $updated,
        ];
    }

    private function syncEvent(int $userId, array $calendar, array $event): ?string
    {
        $eventId = 'gcal_' . $event['id'];
        $title = $event['summary'] ?? 'Untitled Event';
        $description = $event['description'] ?? '';

        $startDate = $this->parseEventDate($event, 'start');
        $endDate = $this->parseEventDate($event, 'end');

        if (!$startDate) {
            return null;
        }

        $boardId = $calendar['board_id'];
        $columnId = $this->findColumnForDate($userId, $boardId, $startDate);

        if (!$columnId) {
            return null;
        }

        $existingCard = $this->cardModel
            ->where('external_id', $eventId)
            ->first();

        $cardData = [
            'column_id' => $columnId,
            'title' => $title,
            'description' => $description,
            'start_date' => $startDate,
            'due_date' => $startDate,
            'is_calendar_event' => true,
            'external_id' => $eventId,
            'external_source' => 'google_calendar',
            'external_data' => json_encode([
                'event_id' => $event['id'],
                'calendar_id' => $calendar['id'],
                'html_link' => $event['htmlLink'] ?? null,
            ]),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($existingCard) {
            $this->cardModel->update($existingCard['id'], $cardData);
            return 'updated';
        }

        $cardData['position'] = $this->getNextPosition($columnId);
        $cardData['created_at'] = date('Y-m-d H:i:s');

        $this->cardModel->insert($cardData);
        return 'created';
    }

    private function parseEventDate(array $event, string $key): ?string
    {
        if (!isset($event[$key])) {
            return null;
        }

        $dateData = $event[$key];

        if (isset($dateData['dateTime'])) {
            return date('Y-m-d H:i:s', strtotime($dateData['dateTime']));
        }

        if (isset($dateData['date'])) {
            return date('Y-m-d H:i:s', strtotime($dateData['date']));
        }

        return null;
    }

    private function findColumnForDate(int $userId, int $boardId, string $date): ?int
    {
        $columns = $this->columnModel->getForBoard($boardId);
        $today = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $thisWeek = date('Y-m-d', strtotime('+7 days'));
        $nextWeek = date('Y-m-d', strtotime('+14 days'));

        $eventDate = date('Y-m-d', strtotime($date));

        foreach ($columns as $column) {
            $name = strtolower($column['title']);

            if ($eventDate === $today && (str_contains($name, 'today'))) {
                return $column['id'];
            }

            if ($eventDate === $tomorrow && (str_contains($name, 'tomorrow'))) {
                return $column['id'];
            }

            if ($eventDate <= $thisWeek && (str_contains($name, 'this week') || str_contains($name, 'week'))) {
                return $column['id'];
            }

            if ($eventDate <= $nextWeek && (str_contains($name, 'next week'))) {
                return $column['id'];
            }
        }

        $dateColumns = array_filter($columns, fn($c) => str_contains(strtolower($c['title']), 'date') ||
                                                         str_contains(strtolower($c['title']), 'calendar'));

        if (!empty($dateColumns)) {
            return array_values($dateColumns)[0]['id'];
        }

        return $columns[0]['id'] ?? null;
    }

    private function getNextPosition(int $columnId): int
    {
        $lastCard = $this->cardModel
            ->where('column_id', $columnId)
            ->orderBy('position', 'DESC')
            ->first();

        return ($lastCard['position'] ?? 0) + 1;
    }

    public function removeCalendarEvents(int $userId, int $calendarId): bool
    {
        $calendar = $this->calendarModel->find($calendarId);

        if (!$calendar || $calendar['user_id'] !== $userId) {
            return false;
        }

        $eventIdPrefix = 'gcal_';

        $cards = $this->cardModel
            ->like('external_id', $eventIdPrefix)
            ->findAll();

        foreach ($cards as $card) {
            $externalData = json_decode($card['external_data'], true);

            if (isset($externalData['calendar_id']) && (int) $externalData['calendar_id'] === $calendarId) {
                $this->cardModel->delete($card['id']);
            }
        }

        return true;
    }
}
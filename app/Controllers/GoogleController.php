<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\GoogleTokenModel;
use App\Models\GoogleCalendarModel;
use App\Services\GoogleAuthService;
use App\Services\CalendarSyncService;

class GoogleController extends BaseController
{
    protected GoogleTokenModel $tokenModel;
    protected GoogleCalendarModel $calendarModel;
    protected GoogleAuthService $authService;
    protected CalendarSyncService $calendarService;

    public function __construct()
    {
        $this->tokenModel = new GoogleTokenModel();
        $this->calendarModel = new GoogleCalendarModel();
        $this->authService = new GoogleAuthService();
        $this->calendarService = new CalendarSyncService();
    }

    public function auth()
    {
        $authUrl = $this->authService->getAuthUrl();
        return redirect()->to($authUrl);
    }

    public function callback()
    {
        $code = $this->request->getGet('code');

        if (!$code) {
            return redirect()->to('boards')->with('error', 'Authorization failed.');
        }

        $tokenData = $this->authService->exchangeCodeForTokens($code);

        if (!$tokenData) {
            return redirect()->to('boards')->with('error', 'Failed to exchange authorization code for tokens.');
        }

        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->to('auth/login');
        }

        $stored = $this->authService->storeTokens($userId, $tokenData);

        if ($stored) {
            return redirect()->to('boards')->with('success', 'Google account connected successfully.');
        }

        return redirect()->to('boards')->with('error', 'Failed to store tokens.');
    }

    public function calendars()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authenticated.']);
        }

        $calendars = $this->calendarService->fetchCalendars($userId);

        return $this->response->setJSON([
            'success' => $calendars !== null,
            'calendars' => $calendars ?? [],
        ]);
    }

    public function syncCalendar()
    {
        $userId = session()->get('user_id');
        $json = $this->request->getJSON(true);

        $googleCalendarId = $json['google_calendar_id'] ?? null;
        $name = $json['name'] ?? '';
        $boardId = $json['board_id'] ?? null;
        $isPrimary = $json['is_primary'] ?? false;

        if (!$googleCalendarId || !$name) {
            return $this->response->setJSON(['success' => false, 'message' => 'Missing required fields.']);
        }

        $existingCalendar = $this->calendarModel
            ->where('user_id', $userId)
            ->where('google_calendar_id', $googleCalendarId)
            ->first();

        if ($existingCalendar) {
            $this->calendarModel->update($existingCalendar['id'], [
                'name' => $name,
                'is_primary' => $isPrimary,
                'board_id' => $boardId,
                'sync_enabled' => true,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $calendarId = $existingCalendar['id'];
        } else {
            $calendarId = $this->calendarModel->insert([
                'user_id' => $userId,
                'google_calendar_id' => $googleCalendarId,
                'name' => $name,
                'is_primary' => $isPrimary,
                'sync_enabled' => true,
                'board_id' => $boardId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return $this->response->setJSON([
            'success' => !!$calendarId,
            'message' => $calendarId ? 'Calendar synced.' : 'Failed to sync calendar.',
        ]);
    }

    public function toggleSync($id)
    {
        $userId = session()->get('user_id');
        $calendar = $this->calendarModel->find($id);

        if (!$calendar || $calendar['user_id'] != $userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Calendar not found.']);
        }

        $newStatus = !$calendar['sync_enabled'];
        $updated = $this->calendarModel->update($id, [
            'sync_enabled' => $newStatus,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'success' => $updated,
            'sync_enabled' => $newStatus,
        ]);
    }

    public function refreshCalendar($id)
    {
        $userId = session()->get('user_id');

        $result = $this->calendarService->syncCalendarEvents($userId, $id);

        return $this->response->setJSON($result);
    }

    public function deleteCalendar($id)
    {
        $userId = session()->get('user_id');
        $calendar = $this->calendarModel->find($id);

        if (!$calendar || $calendar['user_id'] != $userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Calendar not found.']);
        }

        $this->calendarService->removeCalendarEvents($userId, $id);
        $deleted = $this->calendarModel->delete($id);

        return $this->response->setJSON([
            'success' => $deleted,
            'message' => $deleted ? 'Calendar removed.' : 'Failed to remove calendar.',
        ]);
    }

    public function disconnect()
    {
        $userId = session()->get('user_id');
        $deleted = $this->authService->disconnect($userId);

        if ($deleted) {
            $this->calendarModel->where('user_id', $userId)->delete();
        }

        return $this->response->setJSON([
            'success' => $deleted,
            'message' => $deleted ? 'Google account disconnected.' : 'Failed to disconnect.',
        ]);
    }

    public function getConnectedCalendars()
    {
        $userId = session()->get('user_id');
        $calendars = $this->calendarModel->getForUser($userId);

        return $this->response->setJSON(['calendars' => $calendars]);
    }
}
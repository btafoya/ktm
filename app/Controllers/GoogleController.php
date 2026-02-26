<?php

namespace App\Controllers;

use App\Models\GoogleTokenModel;
use App\Models\GoogleCalendarModel;

class GoogleController extends BaseController
{
    protected $googleTokenModel;
    protected $googleCalendarModel;

    public function __construct()
    {
        $this->googleTokenModel = new GoogleTokenModel();
        $this->googleCalendarModel = new GoogleCalendarModel();
    }

    public function auth()
    {
        $clientId = getenv('google.client.id');
        $redirectUri = getenv('google.redirect.uri');

        $scope = urlencode('https://www.googleapis.com/auth/calendar https://www.googleapis.com/auth/gmail.readonly');

        $authUrl = "https://accounts.google.com/o/oauth2/v2/auth?client_id={$clientId}&redirect_uri={$redirectUri}&response_type=code&scope={$scope}&access_type=offline&prompt=consent";

        return redirect()->to($authUrl);
    }

    public function callback()
    {
        $code = $this->request->getGet('code');

        if (!$code) {
            return redirect()->to('boards')->with('error', 'Authorization failed.');
        }

        $clientId = getenv('google.client.id');
        $clientSecret = getenv('google.client.secret');
        $redirectUri = getenv('google.redirect.uri');

        $tokenUrl = 'https://oauth2.googleapis.com/token';

        $postData = http_build_query([
            'code' => $code,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
        ]);

        $ch = curl_init($tokenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

        $response = curl_exec($ch);
        curl_close($ch);

        $tokenData = json_decode($response, true);

        if (!isset($tokenData['access_token'])) {
            return redirect()->to('boards')->with('error', 'Failed to exchange authorization code for tokens.');
        }

        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->to('auth/login');
        }

        $expiresAt = date('Y-m-d H:i:s', time() + $tokenData['expires_in']);

        $existingToken = $this->googleTokenModel->getForUser($userId);
        if ($existingToken) {
            $this->googleTokenModel->update($existingToken['id'], [
                'access_token' => $tokenData['access_token'],
                'refresh_token' => $tokenData['refresh_token'] ?? $existingToken['refresh_token'],
                'expires_at' => $expiresAt,
                'scope' => $tokenData['scope'] ?? '',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        } else {
            $this->googleTokenModel->insert([
                'user_id' => $userId,
                'access_token' => $tokenData['access_token'],
                'refresh_token' => $tokenData['refresh_token'] ?? '',
                'expires_at' => $expiresAt,
                'scope' => $tokenData['scope'] ?? '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return redirect()->to('boards')->with('success', 'Google account connected successfully.');
    }

    public function calendars()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authenticated.']);
        }

        $token = $this->googleTokenModel->getForUser($userId);
        if (!$token) {
            return $this->response->setJSON(['success' => false, 'message' => 'No Google account connected.']);
        }

        $calendarUrl = 'https://www.googleapis.com/calendar/v3/users/me/calendarList';

        $ch = curl_init($calendarUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer {$token['access_token']}"]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 401) {
            return $this->response->setJSON(['success' => false, 'message' => 'Token expired. Please reconnect.']);
        }

        $data = json_decode($response, true);

        return $this->response->setJSON([
            'success' => isset($data['items']),
            'calendars' => $data['items'] ?? [],
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

        $calendarId = $this->googleCalendarModel->insert([
            'user_id' => $userId,
            'google_calendar_id' => $googleCalendarId,
            'name' => $name,
            'is_primary' => $isPrimary,
            'sync_enabled' => true,
            'board_id' => $boardId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'success' => !!$calendarId,
            'message' => $calendarId ? 'Calendar synced.' : 'Failed to sync calendar.',
        ]);
    }

    public function toggleSync($id)
    {
        $userId = session()->get('user_id');
        $calendar = $this->googleCalendarModel->find($id);

        if (!$calendar || $calendar['user_id'] != $userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Calendar not found.']);
        }

        $newStatus = !$calendar['sync_enabled'];
        $updated = $this->googleCalendarModel->update($id, [
            'sync_enabled' => $newStatus,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'success' => $updated,
            'sync_enabled' => $newStatus,
        ]);
    }

    public function disconnect()
    {
        $userId = session()->get('user_id');
        $token = $this->googleTokenModel->getForUser($userId);

        if ($token) {
            $this->googleTokenModel->delete($token['id']);
            $this->googleCalendarModel->where('user_id', $userId)->delete();
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Google account disconnected.',
        ]);
    }
}
<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\GoogleTokenModel;

class GoogleAuthService
{
    private GoogleTokenModel $tokenModel;

    public function __construct()
    {
        $this->tokenModel = new GoogleTokenModel();
    }

    public function getAuthUrl(): string
    {
        $clientId = getenv('google.client.id');
        $redirectUri = getenv('google.redirect.uri');

        $scope = urlencode(
            'https://www.googleapis.com/auth/calendar ' .
            'https://www.googleapis.com/auth/gmail.readonly'
        );

        return "https://accounts.google.com/o/oauth2/v2/auth?" .
            "client_id={$clientId}&" .
            "redirect_uri={$redirectUri}&" .
            "response_type=code&" .
            "scope={$scope}&" .
            "access_type=offline&" .
            "prompt=consent";
    }

    public function exchangeCodeForTokens(string $code): ?array
    {
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
            return null;
        }

        return $tokenData;
    }

    public function storeTokens(int $userId, array $tokenData): bool
    {
        $expiresAt = date('Y-m-d H:i:s', time() + $tokenData['expires_in']);

        $existingToken = $this->tokenModel->getForUser($userId);

        if ($existingToken) {
            return $this->tokenModel->update($existingToken['id'], [
                'access_token' => $tokenData['access_token'],
                'refresh_token' => $tokenData['refresh_token'] ?? $existingToken['refresh_token'],
                'expires_at' => $expiresAt,
                'scope' => $tokenData['scope'] ?? '',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return (bool) $this->tokenModel->insert([
            'user_id' => $userId,
            'access_token' => $tokenData['access_token'],
            'refresh_token' => $tokenData['refresh_token'] ?? '',
            'expires_at' => $expiresAt,
            'scope' => $tokenData['scope'] ?? '',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function getAccessToken(int $userId): ?string
    {
        $token = $this->tokenModel->getForUser($userId);

        if (!$token) {
            return null;
        }

        if ($this->tokenModel->isExpired($userId)) {
            return $this->refreshAccessToken($userId);
        }

        return $token['access_token'];
    }

    public function refreshAccessToken(int $userId): ?string
    {
        $token = $this->tokenModel->getForUser($userId);

        if (!$token || empty($token['refresh_token'])) {
            return null;
        }

        $clientId = getenv('google.client.id');
        $clientSecret = getenv('google.client.secret');

        $tokenUrl = 'https://oauth2.googleapis.com/token';

        $postData = http_build_query([
            'refresh_token' => $token['refresh_token'],
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'grant_type' => 'refresh_token',
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
            return null;
        }

        $expiresAt = date('Y-m-d H:i:s', time() + $tokenData['expires_in']);

        $this->tokenModel->update($token['id'], [
            'access_token' => $tokenData['access_token'],
            'expires_at' => $expiresAt,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return $tokenData['access_token'];
    }

    public function hasValidToken(int $userId): bool
    {
        return $this->tokenModel->getForUser($userId) !== null;
    }

    public function disconnect(int $userId): bool
    {
        $token = $this->tokenModel->getForUser($userId);

        if (!$token) {
            return false;
        }

        return $this->tokenModel->delete($token['id']);
    }
}
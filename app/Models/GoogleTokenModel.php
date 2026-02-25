<?php

namespace App\Models;

use CodeIgniter\Model;

class GoogleTokenModel extends Model
{
    protected $table            = 'google_tokens';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'access_token',
        'refresh_token',
        'expires_at',
        'scope',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get token for user
     */
    public function getForUser(int $userId): ?array
    {
        return $this->where('user_id', $userId)->first();
    }

    /**
     * Store or update token for user
     */
    public function storeToken(int $userId, array $token): bool
    {
        $existing = $this->getForUser($userId);

        $data = [
            'user_id'       => $userId,
            'access_token'  => json_encode($token),
            'refresh_token' => $token['refresh_token'] ?? '',
            'expires_at'    => date('Y-m-d H:i:s', $token['expires_in'] ?? time() + 3600),
            'scope'         => $token['scope'] ?? '',
        ];

        if ($existing) {
            return $this->update($existing['id'], $data);
        }

        return $this->insert($data) !== false;
    }

    /**
     * Delete token for user
     */
    public function deleteForUser(int $userId): bool
    {
        return $this->where('user_id', $userId)->delete();
    }

    /**
     * Check if token needs refresh
     */
    public function needsRefresh(int $userId): bool
    {
        $token = $this->getForUser($userId);

        if (!$token) {
            return false;
        }

        return strtotime($token['expires_at']) < time() + 300; // 5 minute buffer
    }
}
<?php

namespace App\Models;

use CodeIgniter\Model;

class GoogleTokenModel extends Model
{
    protected $table = 'google_tokens';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id', 'access_token', 'refresh_token',
        'expires_at', 'scope', 'created_at', 'updated_at'
    ];

    protected $useTimestamps = false;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getForUser(int $userId): ?array
    {
        return $this->where('user_id', $userId)->first();
    }

    public function isExpired(int $userId): bool
    {
        $token = $this->getForUser($userId);
        if (!$token) {
            return true;
        }
        return strtotime($token['expires_at']) < time() - 60;
    }
}
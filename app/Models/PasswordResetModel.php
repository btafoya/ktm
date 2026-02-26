<?php

namespace App\Models;

use CodeIgniter\Model;

class PasswordResetModel extends Model
{
    protected $table = 'password_resets';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'email', 'token', 'expires_at', 'created_at'
    ];

    protected $useTimestamps = false;
    protected $createdField = 'created_at';

    public function createToken(string $email): string
    {
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + 3600);

        $this->insert([
            'email' => $email,
            'token' => $token,
            'expires_at' => $expiresAt,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $token;
    }

    public function findByToken(string $token): ?array
    {
        return $this->where('token', $token)->first();
    }

    public function isExpired(string $token): bool
    {
        $reset = $this->findByToken($token);
        if (!$reset) {
            return true;
        }
        return strtotime($reset['expires_at']) < time();
    }

    public function deleteByEmail(string $email): bool
    {
        return $this->where('email', $email)->delete() !== false;
    }

    public function deleteToken(string $token): bool
    {
        return $this->where('token', $token)->delete() !== false;
    }
}
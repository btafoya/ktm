<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'email', 'password_hash', 'full_name', 'avatar_url',
        'timezone', 'preferences', 'is_active', 'created_at', 'updated_at'
    ];

    protected bool $allowEmptyInserts = false;

    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $allowCallbacks = true;
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected $validationRules = [
        'email' => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password_hash' => 'required',
        'full_name' => 'permit_empty|string|max_length[255]',
    ];

    protected $validationMessages = [
        'email' => [
            'required' => 'Email is required',
            'valid_email' => 'Please enter a valid email address',
            'is_unique' => 'This email is already registered',
        ],
    ];

    public function findByEmail(string $email): ?array
    {
        return $this->where('email', $email)->first();
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public function setPassword(string $password): string
    {
        return password_hash($password, PASSWORD_ARGON2ID);
    }

    protected function hashPassword(array $data): array
    {
        if (isset($data['data']['password_hash'])) {
            $data['data']['password_hash'] = $this->setPassword($data['data']['password_hash']);
        }
        return $data;
    }

    public function getWithGoogleTokens(int $userId): ?array
    {
        $user = $this->find($userId);
        if (!$user) {
            return null;
        }

        $googleTokenModel = new GoogleTokenModel();
        $tokens = $googleTokenModel->where('user_id', $userId)->findAll();

        $user['google_tokens'] = $tokens;
        return $user;
    }
}
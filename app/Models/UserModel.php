<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['email', 'password', 'display_name', 'created_at', 'updated_at'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'email'      => 'required|valid_email|is_unique[users.email]',
        'password'   => 'required|min_length[8]',
        'display_name'=> 'permit_empty|string|max_length[100]',
    ];
    protected $validationMessages = [
        'email' => [
            'required' => 'Email is required',
            'valid_email' => 'Please enter a valid email address',
            'is_unique' => 'This email is already registered',
        ],
        'password' => [
            'required' => 'Password is required',
            'min_length' => 'Password must be at least 8 characters',
        ],
    ];
    protected $skipValidation = false;

    /**
     * Hash password before inserting
     */
    protected function beforeInsert(array $data): array
    {
        return $this->hashPassword($data);
    }

    /**
     * Hash password before updating if provided
     */
    protected function beforeUpdate(array $data): array
    {
        return $this->hashPassword($data);
    }

    private function hashPassword(array $data): array
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash(
                $data['data']['password'],
                PASSWORD_ARGON2ID,
                ['memory_cost' => 65536, 'time_cost' => 4, 'threads' => 3]
            ) ?: password_hash($data['data']['password'], PASSWORD_BCRYPT);
        }

        return $data;
    }

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?array
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Verify user password
     */
    public function verifyPassword(string $email, string $password): bool
    {
        $user = $this->findByEmail($email);

        if (!$user) {
            return false;
        }

        return password_verify($password, $user['password']);
    }
}
<?php

namespace App\Models;

use CodeIgniter\Model;

class BoardModel extends Model
{
    protected $table            = 'boards';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id', 'name', 'archived_at', 'created_at', 'updated_at'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'user_id' => 'required|integer',
        'name'    => 'required|string|max_length[255]',
    ];
    protected $validationMessages = [
        'name' => [
            'required' => 'Board name is required',
            'max_length' => 'Board name must be less than 255 characters',
        ],
    ];
    protected $skipValidation = false;

    /**
     * Get boards for a user (excluding archived)
     */
    public function getForUser(int $userId, bool $includeArchived = false): array
    {
        $builder = $this->where('user_id', $userId);

        if (!$includeArchived) {
            $builder->where('archived_at IS NULL');
        }

        return $builder->orderBy('created_at', 'DESC')->findAll();
    }

    /**
     * Archive a board
     */
    public function archive(int $id): bool
    {
        return $this->update($id, ['archived_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Unarchive a board
     */
    public function unarchive(int $id): bool
    {
        return $this->update($id, ['archived_at' => null]);
    }

    /**
     * Count user's boards
     */
    public function countForUser(int $userId): int
    {
        return $this->where('user_id', $userId)
            ->where('archived_at IS NULL')
            ->countAllResults();
    }
}
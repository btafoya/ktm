<?php

namespace App\Models;

use CodeIgniter\Model;

class BoardModel extends Model
{
    protected $table = 'boards';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id', 'name', 'description', 'is_public',
        'is_default', 'background_color', 'column_order',
        'created_at', 'updated_at'
    ];

    protected $useTimestamps = false;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getForUser(int $userId): array
    {
        return $this->where('user_id', $userId)
            ->orderBy('is_default', 'DESC')
            ->orderBy('created_at', 'ASC')
            ->findAll();
    }

    public function getWithColumns(int $boardId): ?array
    {
        $board = $this->find($boardId);
        if (!$board) {
            return null;
        }

        $columnModel = new ColumnModel();
        $board['columns'] = $columnModel->getForBoard($boardId);

        return $board;
    }

    public function getDefaultBoard(int $userId): ?array
    {
        return $this->where('user_id', $userId)
            ->where('is_default', true)
            ->first();
    }
}
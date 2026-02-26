<?php

namespace App\Models;

use CodeIgniter\Model;

class ColumnModel extends Model
{
    protected $table = 'columns';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'board_id', 'name', 'color', 'position',
        'created_at', 'updated_at'
    ];

    protected $useTimestamps = false;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getForBoard(int $boardId): array
    {
        return $this->where('board_id', $boardId)
            ->orderBy('position', 'ASC')
            ->findAll();
    }

    public function getWithCards(int $columnId): ?array
    {
        $column = $this->find($columnId);
        if (!$column) {
            return null;
        }

        $cardModel = new CardModel();
        $column['cards'] = $cardModel->getForColumn($columnId);

        return $column;
    }

    public function reorder(int $boardId, array $columnIds): bool
    {
        foreach ($columnIds as $index => $columnId) {
            $this->update($columnId, ['position' => $index]);
        }
        return true;
    }
}
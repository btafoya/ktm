<?php

namespace App\Models;

use CodeIgniter\Model;

class ColumnModel extends Model
{
    protected $table            = 'columns';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'board_id',
        'name',
        'position',
        'is_date_based',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'board_id' => 'required|integer',
        'name'     => 'required|string|max_length[100]',
        'position' => 'required|integer',
    ];
    protected $validationMessages = [
        'name' => [
            'required' => 'Column name is required',
            'max_length' => 'Column name must be less than 100 characters',
        ],
    ];
    protected $skipValidation = false;

    /**
     * Get columns for a board, ordered by position
     */
    public function getForBoard(int $boardId): array
    {
        return $this->where('board_id', $boardId)
            ->orderBy('position', 'ASC')
            ->findAll();
    }

    /**
     * Get date-based columns for a board
     */
    public function getDateBasedForBoard(int $boardId): array
    {
        return $this->where('board_id', $boardId)
            ->where('is_date_based', true)
            ->orderBy('position', 'ASC')
            ->findAll();
    }

    /**
     * Find column by name for a board
     */
    public function findByName(int $boardId, string $name): ?array
    {
        return $this->where('board_id', $boardId)
            ->where('name', $name)
            ->first();
    }

    /**
     * Reorder columns
     */
    public function reorder(array $columnIds): bool
    {
        foreach ($columnIds as $position => $id) {
            $this->update($id, ['position' => $position]);
        }

        return true;
    }

    /**
     * Get maximum position for a board
     */
    public function getMaxPosition(int $boardId): int
    {
        $result = $this->selectMax('position')
            ->where('board_id', $boardId)
            ->first();

        return (int)($result['position'] ?? -1);
    }

    /**
     * Check if board has any columns
     */
    public function boardHasColumns(int $boardId): bool
    {
        return $this->where('board_id', $boardId)->countAllResults() > 0;
    }
}
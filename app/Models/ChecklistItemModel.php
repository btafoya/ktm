<?php

namespace App\Models;

use CodeIgniter\Model;

class ChecklistItemModel extends Model
{
    protected $table = 'checklist_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'card_id', 'title', 'is_completed', 'position',
        'created_at', 'updated_at'
    ];

    protected $useTimestamps = false;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getForCard(int $cardId): array
    {
        return $this->where('card_id', $cardId)
            ->orderBy('position', 'ASC')
            ->findAll();
    }

    public function toggleComplete(int $itemId): bool
    {
        $item = $this->find($itemId);
        if (!$item) {
            return false;
        }

        return $this->update($itemId, ['is_completed' => !$item['is_completed']]);
    }

    public function reorder(int $cardId, array $itemIds): bool
    {
        foreach ($itemIds as $index => $itemId) {
            $this->update($itemId, ['position' => $index]);
        }
        return true;
    }
}
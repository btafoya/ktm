<?php

namespace App\Models;

use CodeIgniter\Model;

class ChecklistItemModel extends Model
{
    protected $table            = 'checklist_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['card_id', 'text', 'completed', 'position', 'created_at'];

    // Dates
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    // Validation
    protected $validationRules = [
        'card_id' => 'required|integer',
        'text'    => 'required|string',
    ];
    protected $validationMessages = [
        'text' => [
            'required' => 'Checklist item text is required',
        ],
    ];
    protected $skipValidation = false;

    /**
     * Get checklist items for a card
     */
    public function getForCard(int $cardId): array
    {
        return $this->where('card_id', $cardId)
            ->orderBy('position', 'ASC')
            ->findAll();
    }

    /**
     * Toggle item completion
     */
    public function toggle(int $id): bool
    {
        $item = $this->find($id);

        if (!$item) {
            return false;
        }

        return $this->update($id, ['completed' => !$item['completed']]);
    }

    /**
     * Get progress for a card's checklist
     */
    public function getProgress(int $cardId): array
    {
        $items = $this->getForCard($cardId);

        if (empty($items)) {
            return ['total' => 0, 'completed' => 0, 'percentage' => 0];
        }

        $completed = count(array_filter($items, fn($item) => $item['completed']));

        return [
            'total' => count($items),
            'completed' => $completed,
            'percentage' => (int) round(($completed / count($items)) * 100),
        ];
    }

    /**
     * Delete all items for a card
     */
    public function deleteForCard(int $cardId): bool
    {
        return $this->where('card_id', $cardId)->delete();
    }

    /**
     * Reorder items in a card
     */
    public function reorder(int $cardId, array $itemIds): bool
    {
        foreach ($itemIds as $position => $id) {
            $this->update($id, ['position' => $position]);
        }

        return true;
    }
}
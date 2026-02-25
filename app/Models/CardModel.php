<?php

namespace App\Models;

use CodeIgniter\Model;

class CardModel extends Model
{
    protected $table            = 'cards';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'column_id',
        'board_id',
        'title',
        'description',
        'color',
        'priority',
        'due_date',
        'type',
        'position',
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
        'column_id' => 'required|integer',
        'board_id'  => 'required|integer',
        'title'     => 'required|string|max_length[255]',
        'color'     => 'permit_empty|string|max_length[7]',
        'priority'  => 'permit_empty|in_list[low,medium,high]',
        'type'      => 'permit_empty|in_list[task,calendar,email]',
    ];
    protected $validationMessages = [
        'title' => [
            'required' => 'Card title is required',
            'max_length' => 'Title must be less than 255 characters',
        ],
    ];
    protected $skipValidation = false;

    /**
     * Get cards for a column, ordered by position
     */
    public function getForColumn(int $columnId): array
    {
        return $this->where('column_id', $columnId)
            ->orderBy('position', 'ASC')
            ->findAll();
    }

    /**
     * Get cards for a board
     */
    public function getForBoard(int $boardId): array
    {
        return $this->where('board_id', $boardId)->findAll();
    }

    /**
     * Get cards with tags and checklist
     */
    public function getWithRelations(int $cardId): ?array
    {
        $card = $this->find($cardId);

        if (!$card) {
            return null;
        }

        $db = db_connect();

        // Get tags
        $tags = $db->table('tags')
            ->select('tags.id, tags.name, tags.color')
            ->join('card_tags', 'card_tags.tag_id = tags.id')
            ->where('card_tags.card_id', $cardId)
            ->get()
            ->getResultArray();

        $card['tags'] = $tags;

        // Get checklist
        $checklist = $db->table('checklist_items')
            ->where('card_id', $cardId)
            ->orderBy('position', 'ASC')
            ->get()
            ->getResultArray();

        $card['checklist'] = $checklist;

        return $card;
    }

    /**
     * Move card to another column
     */
    public function moveToColumn(int $cardId, int $newColumnId, ?int $newPosition = null): bool
    {
        $data = ['column_id' => $newColumnId];

        if ($newPosition !== null) {
            $data['position'] = $newPosition;
        }

        return $this->update($cardId, $data);
    }

    /**
     * Get overdue cards
     */
    public function getOverdue(int $boardId): array
    {
        return $this->where('board_id', $boardId)
            ->where('due_date <', date('Y-m-d H:i:s'))
            ->where('due_date IS NOT NULL')
            ->where('type', 'task')
            ->where('column_id !=', function ($builder) {
                // Exclude cards in "Done" column (assuming last column)
                return $builder->select('id')
                    ->from('columns')
                    ->where('board_id', $boardId)
                    ->orderBy('position', 'DESC')
                    ->limit(1);
            })
            ->findAll();
    }

    /**
     * Reorder cards in a column
     */
    public function reorderInColumn(int $columnId, array $cardIds): bool
    {
        foreach ($cardIds as $position => $id) {
            $this->update($id, ['position' => $position]);
        }

        return true;
    }

    /**
     * Get maximum position for a column
     */
    public function getMaxPosition(int $columnId): int
    {
        $result = $this->selectMax('position')
            ->where('column_id', $columnId)
            ->first();

        return (int)($result['position'] ?? -1);
    }

    /**
     * Search cards
     */
    public function search(int $boardId, string $query): array
    {
        return $this->where('board_id', $boardId)
            ->groupStart()
                ->like('title', $query)
                ->orLike('description', $query)
            ->groupEnd()
            ->findAll();
    }
}
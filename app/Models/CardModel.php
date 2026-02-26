<?php

namespace App\Models;

use CodeIgniter\Model;

class CardModel extends Model
{
    protected $table = 'cards';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'column_id', 'title', 'description', 'position',
        'priority', 'due_date', 'is_completed',
        'google_event_id', 'created_at', 'updated_at'
    ];

    protected $useTimestamps = false;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getForColumn(int $columnId): array
    {
        return $this->where('column_id', $columnId)
            ->orderBy('position', 'ASC')
            ->orderBy('created_at', 'ASC')
            ->findAll();
    }

    public function getWithDetails(int $cardId): ?array
    {
        $card = $this->find($cardId);
        if (!$card) {
            return null;
        }

        $checklistModel = new ChecklistItemModel();
        $card['checklist_items'] = $checklistModel->getForCard($cardId);

        $tagModel = new TagModel();
        $card['tags'] = $tagModel->getForCard($cardId);

        $attachmentModel = new AttachmentModel();
        $card['attachments'] = $attachmentModel->getForCard($cardId);

        $columnModel = new ColumnModel();
        $column = $columnModel->find($card['column_id']);
        $card['column_name'] = $column ? $column['name'] : '';
        $card['board_id'] = $column ? $column['board_id'] : null;

        return $card;
    }

    public function getDueSoon(int $userId, int $days = 3): array
    {
        $db = $this->db;

        $query = $db->table('cards c')
            ->select('c.*')
            ->join('columns col', 'col.id = c.column_id')
            ->join('boards b', 'b.id = col.board_id')
            ->where('b.user_id', $userId)
            ->where('c.is_completed', false)
            ->where('c.due_date IS NOT NULL')
            ->where("c.due_date <= NOW() + INTERVAL '{$days} days'")
            ->orderBy('c.due_date', 'ASC')
            ->get();

        return $query->getResultArray();
    }

    public function getOverdue(int $userId): array
    {
        $db = $this->db;

        $query = $db->table('cards c')
            ->select('c.*')
            ->join('columns col', 'col.id = c.column_id')
            ->join('boards b', 'b.id = col.board_id')
            ->where('b.user_id', $userId)
            ->where('c.is_completed', false)
            ->where('c.due_date <', date('Y-m-d H:i:s'))
            ->orderBy('c.due_date', 'ASC')
            ->get();

        return $query->getResultArray();
    }

    public function reorder(int $columnId, array $cardIds): bool
    {
        foreach ($cardIds as $index => $cardId) {
            $this->update($cardId, ['column_id' => $columnId, 'position' => $index]);
        }
        return true;
    }
}
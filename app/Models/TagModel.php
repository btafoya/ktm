<?php

namespace App\Models;

use CodeIgniter\Model;

class TagModel extends Model
{
    protected $table = 'tags';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id', 'name', 'color', 'created_at', 'updated_at'
    ];

    protected $useTimestamps = false;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getForUser(int $userId): array
    {
        return $this->where('user_id', $userId)
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    public function getForCard(int $cardId): array
    {
        $db = $this->db;

        $query = $db->table('tags t')
            ->select('t.*')
            ->join('card_tags ct', 'ct.tag_id = t.id')
            ->where('ct.card_id', $cardId)
            ->orderBy('t.name', 'ASC')
            ->get();

        return $query->getResultArray();
    }

    public function addTagToCard(int $cardId, int $tagId): bool
    {
        $db = $this->db;

        $exists = $db->table('card_tags')
            ->where('card_id', $cardId)
            ->where('tag_id', $tagId)
            ->countAllResults() > 0;

        if ($exists) {
            return true;
        }

        return $db->table('card_tags')->insert([
            'card_id' => $cardId,
            'tag_id' => $tagId,
        ]);
    }

    public function removeTagFromCard(int $cardId, int $tagId): bool
    {
        $db = $this->db;
        return $db->table('card_tags')
            ->where('card_id', $cardId)
            ->where('tag_id', $tagId)
            ->delete() > 0;
    }

    public function updateCardTags(int $cardId, array $tagIds): bool
    {
        $db = $this->db;

        $db->table('card_tags')->where('card_id', $cardId)->delete();

        foreach ($tagIds as $tagId) {
            $db->table('card_tags')->insert([
                'card_id' => $cardId,
                'tag_id' => $tagId,
            ]);
        }

        return true;
    }
}
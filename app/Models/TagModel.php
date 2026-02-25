<?php

namespace App\Models;

use CodeIgniter\Model;

class TagModel extends Model
{
    protected $table            = 'tags';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'color', 'created_at'];

    // Dates
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    // Validation
    protected $validationRules = [
        'name'  => 'required|string|max_length[50]|is_unique[tags.name]',
        'color' => 'permit_empty|string|max_length[7]',
    ];
    protected $validationMessages = [
        'name' => [
            'required' => 'Tag name is required',
            'max_length' => 'Tag name must be less than 50 characters',
            'is_unique' => 'This tag name already exists',
        ],
    ];
    protected $skipValidation = false;

    /**
     * Get all tags ordered by name
     */
    public function getAll(): array
    {
        return $this->orderBy('name', 'ASC')->findAll();
    }

    /**
     * Get tags for a card
     */
    public function getForCard(int $cardId): array
    {
        return $this->select('tags.id, tags.name, tags.color')
            ->join('card_tags', 'card_tags.tag_id = tags.id')
            ->where('card_tags.card_id', $cardId)
            ->findAll();
    }

    /**
     * Get or create tag by name
     */
    public function getOrCreate(string $name, string $color = '#6c757d'): array
    {
        $tag = $this->where('name', $name)->first();

        if (!$tag) {
            $this->insert([
                'name' => $name,
                'color' => $color,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $tag = [
                'id' => $this->getInsertID(),
                'name' => $name,
                'color' => $color,
            ];
        }

        return $tag;
    }

    /**
     * Attach tag to card
     */
    public function attachToCard(int $cardId, int $tagId): bool
    {
        $db = db_connect();

        // Check if already attached
        $existing = $db->table('card_tags')
            ->where('card_id', $cardId)
            ->where('tag_id', $tagId)
            ->get()
            ->getRowArray();

        if ($existing) {
            return true;
        }

        return $db->table('card_tags')->insert([
            'card_id' => $cardId,
            'tag_id' => $tagId,
        ]);
    }

    /**
     * Detach tag from card
     */
    public function detachFromCard(int $cardId, int $tagId): bool
    {
        $db = db_connect();

        return $db->table('card_tags')
            ->where('card_id', $cardId)
            ->where('tag_id', $tagId)
            ->delete() > 0;
    }
}
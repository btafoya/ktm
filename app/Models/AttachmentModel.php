<?php

namespace App\Models;

use CodeIgniter\Model;

class AttachmentModel extends Model
{
    protected $table = 'attachments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'card_id', 'file_name', 'file_path', 'file_size',
        'mime_type', 'created_at'
    ];

    protected $useTimestamps = false;
    protected $createdField = 'created_at';

    public function getForCard(int $cardId): array
    {
        return $this->where('card_id', $cardId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function deleteForCard(int $cardId): bool
    {
        return $this->where('card_id', $cardId)->delete() !== false;
    }
}
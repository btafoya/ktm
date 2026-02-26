<?php

namespace App\Models;

use CodeIgniter\Model;

class EmailModel extends Model
{
    protected $table = 'emails';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'card_id', 'gmail_message_id', 'thread_id', 'sender_email',
        'sender_name', 'subject', 'snippet', 'body', 'received_at',
        'created_at'
    ];

    protected $useTimestamps = false;
    protected $createdField = 'created_at';

    public function getForCard(int $cardId): array
    {
        return $this->where('card_id', $cardId)
            ->orderBy('received_at', 'DESC')
            ->findAll();
    }

    public function findByGmailId(string $gmailMessageId): ?array
    {
        return $this->where('gmail_message_id', $gmailMessageId)->first();
    }
}
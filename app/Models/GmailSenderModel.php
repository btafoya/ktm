<?php

namespace App\Models;

use CodeIgniter\Model;

class GmailSenderModel extends Model
{
    protected $table = 'gmail_senders';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id', 'email', 'name', 'card_id', 'column_id',
        'keyword', 'is_active', 'created_at', 'updated_at'
    ];

    protected $useTimestamps = false;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getForUser(int $userId): array
    {
        return $this->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function getActiveForUser(int $userId): array
    {
        return $this->where('user_id', $userId)
            ->where('is_active', true)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function findMatchingCard(string $senderEmail, string $subject, int $userId): ?array
    {
        $senders = $this->getActiveForUser($userId);

        foreach ($senders as $sender) {
            $emailMatch = strtolower($sender['email']) === strtolower($senderEmail);

            $keywordMatch = empty($sender['keyword'])
                ? true
                : stripos($subject, $sender['keyword']) !== false;

            if ($emailMatch && $keywordMatch) {
                if ($sender['card_id']) {
                    return [
                        'type' => 'card',
                        'target_id' => $sender['card_id'],
                    ];
                } elseif ($sender['column_id']) {
                    return [
                        'type' => 'column',
                        'target_id' => $sender['column_id'],
                    ];
                }
            }
        }

        return null;
    }
}
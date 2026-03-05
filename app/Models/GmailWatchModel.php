<?php

namespace App\Models;

use CodeIgniter\Model;

class GmailWatchModel extends Model
{
    protected $table = 'gmail_watches';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id', 'watch_id', 'history_id', 'topic_resource_id',
        'expiration', 'is_active', 'created_at', 'updated_at'
    ];

    protected $useTimestamps = false;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getForUser(int $userId): ?array
    {
        return $this->where('user_id', $userId)
            ->where('is_active', true)
            ->first();
    }

    public function updateHistoryId(int $userId, string $historyId): bool
    {
        return $this->where('user_id', $userId)
            ->update(['history_id' => $historyId, 'updated_at' => date('Y-m-d H:i:s')]);
    }

    public function deactivate(int $userId): bool
    {
        return $this->where('user_id', $userId)
            ->update(['is_active' => false]);
    }

    public function createWatch(int $userId, string $watchId, string $historyId, string $topicResourceId, int $expirationTimestamp): int
    {
        $this->deactivate($userId);

        return $this->insert([
            'user_id' => $userId,
            'watch_id' => $watchId,
            'history_id' => $historyId,
            'topic_resource_id' => $topicResourceId,
            'expiration' => date('Y-m-d H:i:s', $expirationTimestamp / 1000),
            'is_active' => true,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function getExpiredWatches(): array
    {
        return $this->where('is_active', true)
            ->where('expiration <', date('Y-m-d H:i:s'))
            ->findAll();
    }

    public function deleteByUserId(int $userId): bool
    {
        return $this->where('user_id', $userId)->delete();
    }
}
<?php

namespace App\Models;

use CodeIgniter\Model;

class JobModel extends Model
{
    protected $table = 'jobs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'type', 'payload', 'status', 'attempts',
        'error_message', 'scheduled_at', 'started_at',
        'completed_at', 'created_at'
    ];

    protected $useTimestamps = false;
    protected $createdField = 'created_at';

    public function createJob(string $type, array $payload, ?\DateTimeInterface $scheduledAt = null): int
    {
        return $this->insert([
            'type' => $type,
            'payload' => json_encode($payload),
            'status' => 'pending',
            'attempts' => 0,
            'scheduled_at' => $scheduledAt ? $scheduledAt->format('Y-m-d H:i:s') : date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function getNextPendingJob(): ?array
    {
        return $this->where('status', 'pending')
            ->where('scheduled_at <=', date('Y-m-d H:i:s'))
            ->orderBy('scheduled_at', 'ASC')
            ->first();
    }

    public function markStarted(int $jobId): bool
    {
        return $this->update($jobId, [
            'status' => 'running',
            'started_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function markCompleted(int $jobId): bool
    {
        return $this->update($jobId, [
            'status' => 'completed',
            'completed_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function markFailed(int $jobId, string $errorMessage): bool
    {
        $job = $this->find($jobId);
        if (!$job) {
            return false;
        }

        $newAttempts = $job['attempts'] + 1;
        $maxAttempts = 3;

        if ($newAttempts >= $maxAttempts) {
            return $this->update($jobId, [
                'status' => 'failed',
                'attempts' => $newAttempts,
                'error_message' => $errorMessage,
            ]);
        }

        return $this->update($jobId, [
            'status' => 'pending',
            'attempts' => $newAttempts,
            'error_message' => $errorMessage,
            'scheduled_at' => date('Y-m-d H:i:s', time() + pow(2, $newAttempts) * 60),
        ]);
    }

    public function getFailedJobs(int $limit = 100): array
    {
        return $this->where('status', 'failed')
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }
}
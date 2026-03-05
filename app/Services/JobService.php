<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\JobModel;
use App\Models\CardModel;

class JobService
{
    private JobModel $jobModel;
    private CalendarSyncService $calendarService;
    private GmailSyncService $gmailService;
    private CardModel $cardModel;

    private const JOB_HANDLERS = [
        'calendar_sync' => 'handleCalendarSync',
        'gmail_sync' => 'handleGmailSync',
        'renew_gmail_watch' => 'handleRenewGmailWatch',
        'send_due_date_reminder' => 'handleDueDateReminder',
        'cleanup_old_jobs' => 'handleCleanup',
        'refresh_google_tokens' => 'handleTokenRefresh',
    ];

    public function __construct()
    {
        $this->jobModel = new JobModel();
        $this->calendarService = new CalendarSyncService();
        $this->gmailService = new GmailSyncService();
        $this->cardModel = new CardModel();
    }

    public function dispatch(string $type, array $payload = [], ?\DateTimeInterface $scheduledAt = null): int
    {
        return $this->jobModel->createJob($type, $payload, $scheduledAt);
    }

    public function dispatchLater(string $type, array $payload, int $delaySeconds): int
    {
        $scheduledAt = new \DateTime("+{$delaySeconds} seconds");
        return $this->dispatch($type, $payload, $scheduledAt);
    }

    public function runNextJob(): bool
    {
        $job = $this->jobModel->getNextPendingJob();

        if (!$job) {
            return false;
        }

        $this->jobModel->markStarted($job['id']);

        try {
            $handler = self::JOB_HANDLERS[$job['type']] ?? null;

            if (!$handler || !method_exists($this, $handler)) {
                throw new \Exception("No handler for job type: {$job['type']}");
            }

            $payload = json_decode($job['payload'], true) ?? [];
            $this->{$handler}($payload);

            $this->jobModel->markCompleted($job['id']);
            return true;
        } catch (\Throwable $e) {
            $this->jobModel->markFailed($job['id'], $e->getMessage());
            log_message('error', "Job {$job['id']} failed: " . $e->getMessage());
            return false;
        }
    }

    public function runPendingJobs(int $limit = 10): array
    {
        $results = ['processed' => 0, 'successful' => 0, 'failed' => 0];

        for ($i = 0; $i < $limit; $i++) {
            if (!$this->runNextJob()) {
                break;
            }

            $results['processed']++;
        }

        return $results;
    }

    private function handleCalendarSync(array $payload): void
    {
        $userId = (int) ($payload['user_id'] ?? 0);
        $calendarId = (int) ($payload['calendar_id'] ?? 0);

        if ($userId && $calendarId) {
            $this->calendarService->syncCalendarEvents($userId, $calendarId);
        }
    }

    private function handleGmailSync(array $payload): void
    {
        $userId = (int) ($payload['user_id'] ?? 0);

        if ($userId) {
            $this->gmailService->fetchEmails($userId);
        }
    }

    private function handleRenewGmailWatch(array $payload): void
    {
        $userId = (int) ($payload['user_id'] ?? 0);

        if ($userId) {
            $this->gmailService->setWatch($userId);
        }
    }

    private function handleDueDateReminder(array $payload): void
    {
        $userId = (int) ($payload['user_id'] ?? 0);

        if (!$userId) {
            return;
        }

        $today = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime('+1 day'));

        $cardsDueToday = $this->cardModel
            ->select('cards.*, boards.title as board_title')
            ->join('columns', 'columns.id = cards.column_id')
            ->join('boards', 'boards.id = columns.board_id')
            ->where('boards.user_id', $userId)
            ->where('cards.due_date >=', $today . ' 00:00:00')
            ->where('cards.due_date <=', $today . ' 23:59:59')
            ->where('cards.due_date_notified', 0)
            ->findAll();

        $cardsDueTomorrow = $this->cardModel
            ->select('cards.*, boards.title as board_title')
            ->join('columns', 'columns.id = cards.column_id')
            ->join('boards', 'boards.id = columns.board_id')
            ->where('boards.user_id', $userId)
            ->where('cards.due_date >=', $tomorrow . ' 00:00:00')
            ->where('cards.due_date <=', $tomorrow . ' 23:59:59')
            ->where('cards.due_date_notified', 0)
            ->findAll();

        foreach (array_merge($cardsDueToday, $cardsDueTomorrow) as $card) {
            $this->cardModel->update($card['id'], ['due_date_notified' => 1]);

            log_message('info', "Due date reminder for card {$card['id']}: {$card['title']}");
        }
    }

    private function handleCleanup(array $payload): void
    {
        $days = (int) ($payload['days'] ?? 30);

        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        $completedJobs = $this->jobModel
            ->where('status', 'completed')
            ->where('completed_at <', $cutoffDate)
            ->findAll();

        foreach ($completedJobs as $job) {
            $this->jobModel->delete($job['id']);
        }
    }

    private function handleTokenRefresh(array $payload): void
    {
        $authService = new GoogleAuthService();
        $tokenModel = new \App\Models\GoogleTokenModel();

        $tokens = $tokenModel->findAll();

        foreach ($tokens as $token) {
            $userId = (int) $token['user_id'];

            if ($tokenModel->isExpired($userId)) {
                $authService->refreshAccessToken($userId);
            }
        }
    }
}
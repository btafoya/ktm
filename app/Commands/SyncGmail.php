<?php

declare(strict_types=1);

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\GmailSyncService;
use App\Services\JobService;

class SyncGmail extends BaseCommand
{
    protected $group = 'Sync';
    protected $name = 'sync:gmail';
    protected $description = 'Sync emails from Gmail for configured senders.';

    public function run(array $params)
    {
        $userId = (int) (CLI::getOption('user-id') ?? CLI::getOption('u') ?? 0);
        $queue = CLI::getOption('queue') ?? false;

        CLI::write('Starting Gmail sync...', 'cyan');

        if ($queue) {
            $jobService = new JobService();
            $jobService->dispatch('gmail_sync', ['user_id' => $userId]);
            CLI::write('Gmail sync job queued.', 'green');
            return EXIT_SUCCESS;
        }

        $gmailService = new GmailSyncService();
        $result = $gmailService->fetchEmails($userId);

        if ($result['success']) {
            CLI::write($result['message'], 'green');
            CLI::write("Created: {$result['created']}, Attached: {$result['attached']}", 'light_gray');
            return EXIT_SUCCESS;
        }

        CLI::error($result['message']);
        return EXIT_FAILURE;
    }
}
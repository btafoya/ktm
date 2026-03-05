<?php

declare(strict_types=1);

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\JobService;

class SendReminders extends BaseCommand
{
    protected $group = 'Maintenance';
    protected $name = 'reminders:send';
    protected $description = 'Send due date reminders for cards.';

    public function run(array $params)
    {
        $userId = (int) (CLI::getOption('user-id') ?? CLI::getOption('u') ?? 0);
        $queue = CLI::getOption('queue') ?? false;

        CLI::write('Sending due date reminders...', 'cyan');

        if ($queue) {
            $jobService = new JobService();
            $jobService->dispatch('send_due_date_reminder', ['user_id' => $userId]);
            CLI::write('Reminder job queued.', 'green');
            return EXIT_SUCCESS;
        }

        $jobService = new JobService();
        $jobService->runPendingJobs(1);

        CLI::write('Reminders sent.', 'green');
        return EXIT_SUCCESS;
    }
}
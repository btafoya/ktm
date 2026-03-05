<?php

declare(strict_types=1);

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\JobService;

class CleanupJobs extends BaseCommand
{
    protected $group = 'Maintenance';
    protected $name = 'jobs:cleanup';
    protected $description = 'Clean up old completed jobs from the database.';

    public function run(array $params)
    {
        $days = (int) (CLI::getOption('days') ?? CLI::getOption('d') ?? 30);
        $queue = CLI::getOption('queue') ?? false;

        CLI::write("Cleaning up jobs older than {$days} days...", 'cyan');

        if ($queue) {
            $jobService = new JobService();
            $jobService->dispatch('cleanup_old_jobs', ['days' => $days]);
            CLI::write('Cleanup job queued.', 'green');
            return EXIT_SUCCESS;
        }

        $jobService = new JobService();
        $jobService->runPendingJobs(1);

        CLI::write('Cleanup completed.', 'green');
        return EXIT_SUCCESS;
    }
}
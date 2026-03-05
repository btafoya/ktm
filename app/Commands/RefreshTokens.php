<?php

declare(strict_types=1);

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\JobService;

class RefreshTokens extends BaseCommand
{
    protected $group = 'Maintenance';
    protected $name = 'tokens:refresh';
    protected $description = 'Refresh expired Google access tokens.';

    public function run(array $params)
    {
        $queue = CLI::getOption('queue') ?? false;

        CLI::write('Refreshing Google tokens...', 'cyan');

        if ($queue) {
            $jobService = new JobService();
            $jobService->dispatch('refresh_google_tokens');
            CLI::write('Token refresh job queued.', 'green');
            return EXIT_SUCCESS;
        }

        $jobService = new JobService();
        $jobService->runPendingJobs(1);

        CLI::write('Token refresh completed.', 'green');
        return EXIT_SUCCESS;
    }
}
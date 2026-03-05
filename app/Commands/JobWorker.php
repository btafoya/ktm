<?php

declare(strict_types=1);

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\JobService;

class JobWorker extends BaseCommand
{
    protected $group = 'Jobs';
    protected $name = 'jobs:work';
    protected $description = 'Process pending jobs from the queue. Run continuously with --daemon flag.';

    public function run(array $params)
    {
        $daemon = $params['daemon'] ?? CLI::getOption('daemon') ?? false;
        $sleep = (int) (CLI::getOption('sleep') ?? 5);
        $limit = (int) (CLI::getOption('limit') ?? 10);
        $maxRuns = (int) (CLI::getOption('max-runs') ?? 0);

        $jobService = new JobService();

        CLI::write('Job Worker Started', 'green');

        $runCount = 0;

        try {
            while (true) {
                $results = $jobService->runPendingJobs($limit);

                if ($results['processed'] > 0) {
                    CLI::write(
                        "Processed: {$results['processed']} jobs",
                        'light_gray'
                    );
                }

                $runCount++;

                if ($maxRuns > 0 && $runCount >= $maxRuns) {
                    CLI::write("Reached max runs ({$maxRuns}), exiting.", 'yellow');
                    break;
                }

                if (!$daemon) {
                    break;
                }

                sleep($sleep);
            }
        } catch (\Throwable $e) {
            CLI::error("Worker crashed: " . $e->getMessage());
            log_message('critical', "Job worker crashed: " . $e->getMessage());
            return EXIT_FAILURE;
        }

        CLI::write('Job Worker Finished', 'green');
        return EXIT_SUCCESS;
    }
}
<?php

declare(strict_types=1);

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\GmailSyncService;

class RenewGmailWatches extends BaseCommand
{
    protected $group = 'Maintenance';
    protected $name = 'gmail:renew-watches';
    protected $description = 'Renew expired Gmail watch subscriptions.';

    public function run(array $params)
    {
        CLI::write('Checking for expired Gmail watches...', 'cyan');

        $gmailService = new GmailSyncService();
        $result = $gmailService->renewExpiredWatches();

        CLI::write("Renewed {$result['renewed']} of {$result['total']} expired watches.", 'green');

        if ($result['renewed'] < $result['total']) {
            $failed = $result['total'] - $result['renewed'];
            CLI::write("Failed to renew {$failed} watches.", 'yellow');
        }

        return EXIT_SUCCESS;
    }
}
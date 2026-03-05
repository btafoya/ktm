<?php

declare(strict_types=1);

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\CalendarSyncService;
use App\Services\JobService;
use App\Models\GoogleCalendarModel;

class SyncCalendar extends BaseCommand
{
    protected $group = 'Sync';
    protected $name = 'sync:calendar';
    protected $description = 'Sync events from Google Calendar.';

    public function run(array $params)
    {
        $userId = (int) (CLI::getOption('user-id') ?? CLI::getOption('u') ?? 0);
        $calendarId = (int) (CLI::getOption('calendar-id') ?? CLI::getOption('c') ?? 0);
        $queue = CLI::getOption('queue') ?? false;
        $all = CLI::getOption('all') ?? false;

        CLI::write('Starting Calendar sync...', 'cyan');

        $calendarService = new CalendarSyncService();
        $jobService = new JobService();

        if ($all) {
            $calendarModel = new GoogleCalendarModel();
            $calendars = $calendarModel->where('sync_enabled', true)->findAll();

            if (empty($calendars)) {
                CLI::write('No enabled calendars found.', 'yellow');
                return EXIT_SUCCESS;
            }

            foreach ($calendars as $calendar) {
                if ($queue) {
                    $jobService->dispatch('calendar_sync', [
                        'user_id' => $calendar['user_id'],
                        'calendar_id' => $calendar['id'],
                    ]);
                    CLI::write("Queued calendar sync for calendar ID: {$calendar['id']}", 'green');
                } else {
                    $result = $calendarService->syncCalendarEvents($calendar['user_id'], $calendar['id']);
                    CLI::write("Calendar ID {$calendar['id']}: {$result['message']}", 'green');
                }
            }

            return EXIT_SUCCESS;
        }

        if (!$userId || !$calendarId) {
            CLI::error('Please provide user-id and calendar-id options.');
            CLI::write('Example: php spark sync:calendar --user-id=1 --calendar-id=1', 'light_gray');
            return EXIT_FAILURE;
        }

        if ($queue) {
            $jobService->dispatch('calendar_sync', ['user_id' => $userId, 'calendar_id' => $calendarId]);
            CLI::write('Calendar sync job queued.', 'green');
            return EXIT_SUCCESS;
        }

        $result = $calendarService->syncCalendarEvents($userId, $calendarId);

        if ($result['success']) {
            CLI::write($result['message'], 'green');
            return EXIT_SUCCESS;
        }

        CLI::error($result['message']);
        return EXIT_FAILURE;
    }
}
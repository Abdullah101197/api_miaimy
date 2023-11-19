<?php

namespace App\Console;

use App\Helpers\DeleteMeeting;
use App\Models\ZoomMeeting;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;


class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected $commands = [
        Commands\MeetCron::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('meeting:cron')
            ->daily();
    }
    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}

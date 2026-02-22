<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\UpdateReleaseAndCrs::class,
        \App\Console\Commands\UpdateToNextStatusAsCalendar::class, // Fixed class name
        \App\Console\Commands\KickOffMeetingStatusUpdate::class,
        \App\Console\Commands\Reject_business_approvals::class,
        //  \App\Console\Commands\EwsListenerCommand::class,

    ];

    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('update_releae_and_crs')->daily();
        $schedule->command('CalendarUpdateStatus:run')->daily();
        $schedule->command('email:process-approvals')->everyFiveMinutes()->withoutOverlapping()->runInBackground();
        $schedule->command('cab:approve-users')->daily();
        $schedule->command('cr:update-kickoff-status')
            ->everyFiveMinutes()
            ->withoutOverlapping()
            ->runInBackground();
        $schedule->command('cron:escalation')->everyFiveMinutes();
        $schedule->command('auto:reject-cr')->dailyAt('00:00');
        $schedule->command('cr:send-hold-reminders')->dailyAt('08:00')->withoutOverlapping();

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}

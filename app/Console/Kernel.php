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
        commands\CreateInvoiceCron::class,
        commands\TokenInterCron::class,
        commands\GenerateInvoiceCron::class,
        commands\RememberInvoiceCron::class,
        commands\StatusInterCron::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('createinvoice:cron')->twiceDaily(1, 4);
        $schedule->command('tokeninter:cron')->hourly();
        $schedule->command('generateinvoice:cron')->everyTwoMinutes();
        $schedule->command('rememberinvoice:cron')->twiceDaily(9, 14);
        $schedule->command('statusinter:cron')->everyThirtyMinutes();

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

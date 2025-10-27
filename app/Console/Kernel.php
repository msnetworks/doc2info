<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\HeroCase; // Import the HeroCase model
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    // protected function schedule(Schedule $schedule)
    // {
    //     // $schedule->command('inspire')->hourly();
    // }
    
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            // Use the HeroCase model to update the statuses.
            $updatedRows = HeroCase::where('status', 1156)
                ->update(['status' => 1]);
    
            // Log the update.
            Log::info('Scheduler: Updated ' . $updatedRows . ' hero_cases status from 1156 to 1 using Eloquent model.');
        })->dailyAt('00:25');
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

<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('app:fetch-bestsellers --all')
            ->weekly()
            ->sundays()
            ->at('01:00');

        $schedule->command('app:fetch-movie-reviews --pages=10')
            ->dailyAt('02:00');
    }
}

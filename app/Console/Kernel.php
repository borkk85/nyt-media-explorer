<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
        
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('nyt:fetch-bestsellers')
                 ->weekly()
                 ->sundays()
                 ->at('01:00')
                 ->withoutOverlapping();
        
        $schedule->command('nyt:fetch-movie-reviews 2')
                 ->dailyAt('02:00')
                 ->withoutOverlapping();
    }
    
}
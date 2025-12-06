<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\ImportExampleProducts;

class Kernel extends ConsoleKernel
{
    /**
     * Register custom commands.
     *
     * @var array
     */
    protected $commands = [
        ImportExampleProducts::class,
        \App\Console\Commands\BackfillAttributesHash::class,
        \App\Console\Commands\ReportVariationDuplicates::class,
    ];
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Processar emails automáticos a cada 5 minutos
        $schedule->command('emails:process')->everyFiveMinutes();
        
        // Processar notas fiscais a cada 10 minutos
        $schedule->command('fiscal:process')->everyTenMinutes();
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

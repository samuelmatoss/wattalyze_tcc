<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use app\Jobs\GenerateAggregatesJob;
use app\Jobs\AggregateDataJob;
use app\Jobs\CleanupOldDataJob;
use App\Models\Report;
use App\Jobs\GenerateReportJob;
use App\Jobs\CheckAlertsJob;
use Illuminate\Foundation\Bus\Dispatchable;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {

        // Verificar alertas a cada 5 minutos
        $schedule->job(new CheckAlertsJob())
            ->everyFiveMinutes();



        // Processar relatórios agendados
        $schedule->job(function () {
            Report::where('is_scheduled', true)
                ->where('next_generation', '<=', now())
                ->each(function ($report) {
                    GenerateReportJob::dispatch($report);
                });
        })->everyTenSeconds();
        
    }
    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
    protected $commands = [
        \App\Console\Commands\CreateMeasurement::class,
       
    ];


}

<?php

namespace App\Console\Commands;

use App\Jobs\GenerateAggregatesJob;
use App\Models\Device;
use Illuminate\Console\Command;

class GenerateAggregates extends Command
{
    protected $signature = 'energy:aggregate 
                            {period : Period type (hourly, daily, weekly, monthly)}
                            {--device= : Generate for specific device}
                            {--all-periods : Generate for all periods}';
    
    protected $description = 'Generate energy consumption aggregates';

    public function handle()
    {
        $period = $this->argument('period');
        $deviceId = $this->option('device');
        
        if ($this->option('all-periods')) {
            $periods = ['hourly', 'daily', 'weekly', 'monthly'];
        } else {
            $periods = [$period];
        }
        
        foreach ($periods as $p) {
            $this->info("Generating $p aggregates...");
            
            if ($deviceId) {
                GenerateAggregatesJob::dispatch($p, $deviceId);
                $this->info("Job dispatched for device $deviceId and period $p.");
            } else {
                $devices = Device::all();
                $bar = $this->output->createProgressBar($devices->count());
                
                foreach ($devices as $device) {
                    GenerateAggregatesJob::dispatch($p, $device->id);
                    $bar->advance();
                }
                
                $bar->finish();
                $this->info("\nJobs dispatched for all devices and period $p.");
            }
        }
    }
}
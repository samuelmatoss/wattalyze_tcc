<?php

namespace App\Console\Commands;

use App\Jobs\ProcessEnergyDataJob;
use App\Models\Device;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;

class ProcessEnergyData extends Command
{
    protected $signature = 'energy:process {--device= : Process specific device}';
    protected $description = 'Process pending energy data from devices';

    public function handle()
    {
        $deviceId = $this->option('device');
        
        $query = Device::query();
        
        if ($deviceId) {
            $query->where('id', $deviceId);
        } else {
            $query->where('last_seen_at', '>', now()->subMinutes(30));
        }
        
        $devices = $query->get();
        
        $this->info("Processing data for {$devices->count()} devices");
        $bar = $this->output->createProgressBar($devices->count());
        /** @var \App\Models\Device $device */
        foreach ($devices as $device) {
            try {
                
                // Simulando obtenção de dados pendentes (em produção seria de um broker de mensagens)
                $pendingData = $this->getPendingData($device);
                
                foreach ($pendingData as $data) {
                    ProcessEnergyDataJob::dispatch($device->id, $data);
                }
                
                $bar->advance();
                
            } catch (\Exception $e) {
                Log::error("Error processing device {$device->id}: " . $e->getMessage());
                $this->error("Error processing device {$device->id}: " . $e->getMessage());
            }
        }
        
        $bar->finish();
        $this->info("\nData processing completed");
    }
    
    protected function getPendingData(Device $device)
    {
        // Em produção, isso viria de um RabbitMQ, Kafka, etc.
        return [
            [
                'consumption_kwh' => 0.123456,
                'instantaneous_power' => 245.67,
                'timestamp' => now()->toDateTimeString()
            ],
            // ... mais dados
        ];
    }
}
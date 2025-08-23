<?php

namespace App\Console\Commands;

use App\Models\EnergyConsumption;
use Illuminate\Console\Command;
use InfluxDB2\Client;
use InfluxDB2\Model\WritePrecision;
use InfluxDB2\Point;

class MigrateDataToInflux extends Command
{
    protected $signature = 'influx:migrate 
                            {--chunk=1000 : Records per chunk}
                            {--start= : Start date}
                            {--end= : End date}';
    
    protected $description = 'Migrate historical data to InfluxDB';

    public function handle()
    {
        $client = new Client([
            'url' => config('influxdb.url'),
            'token' => config('influxdb.token'),
        ]);
        
        $writeApi = $client->createWriteApi();
        $bucket = config('influxdb.bucket');
        $org = config('influxdb.org');
        
        $query = EnergyConsumption::query();
        
        if ($start = $this->option('start')) {
            $query->where('timestamp', '>=', $start);
        }
        
        if ($end = $this->option('end')) {
            $query->where('timestamp', '<=', $end);
        }
        
        $total = $query->count();
        $this->info("Migrating $total records to InfluxDB...");
        
        $bar = $this->output->createProgressBar($total);
        $chunkSize = $this->option('chunk');
        
        $query->chunkById($chunkSize, function ($records) use ($writeApi, $bucket, $org, $bar) {
            $points = [];
            
            foreach ($records as $record) {
                $points[] = Point::measurement('energy_consumption')
                    ->addTag('device_id', $record->device_id)
                    ->addTag('environment_id', $record->device->environment_id)
                    ->addField('consumption_kwh', $record->consumption_kwh)
                    ->addField('instantaneous_power', $record->instantaneous_power)
                    ->addField('voltage', $record->voltage)
                    ->addField('current', $record->current)
                    ->addField('power_factor', $record->power_factor)
                    ->addField('temperature', $record->temperature)
                    ->addField('humidity', $record->humidity)
                    ->addField('is_peak_hour', $record->is_peak_hour)
                    ->time($record->timestamp->getTimestamp(), WritePrecision::S);
            }
            
            $writeApi->write($points, WritePrecision::S, $bucket, $org);
            $bar->advance(count($records));
        });
        
        $bar->finish();
        $this->info("\nData migration completed.");
    }
}
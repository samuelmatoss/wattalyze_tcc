<?php

namespace App\Jobs;

use App\Models\EnergyConsumption;
use App\Models\EnergyConsumptionAggregate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use App\Models\Device;
class AggregateDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $monthsToAggregate;

    public function __construct($monthsToAggregate = 6)
    {
        $this->monthsToAggregate = $monthsToAggregate;
    }

    public function handle()
    {
        $cutoffDate = now()->subMonths($this->monthsToAggregate)->startOfMonth();
        
        // Gerar agregados mensais para dados antigos
        $this->generateMonthlyAggregates($cutoffDate);
        
        // Migrar dados brutos para armazenamento de longo prazo (InfluxDB)
        $this->migrateToLongTermStorage($cutoffDate);
    }

    protected function generateMonthlyAggregates($cutoffDate)
    {
        $devices = Device::all();
        
        foreach ($devices as $device) {
            $monthlyData = EnergyConsumptionAggregate::where('device_id', $device->id)
                ->where('period_type', 'daily')
                ->where('period_start', '<', $cutoffDate)
                ->select(
                    DB::raw('device_id'),
                    DB::raw("DATE_FORMAT(period_start, '%Y-%m-01') as month_start"),
                    DB::raw('SUM(total_consumption_kwh) as total_consumption'),
                    DB::raw('AVG(avg_power) as avg_power'),
                    DB::raw('MAX(max_power) as max_power'),
                    DB::raw('MIN(min_power) as min_power'),
                    DB::raw('SUM(total_cost) as total_cost'),
                    DB::raw('SUM(peak_consumption_kwh) as peak_consumption'),
                    DB::raw('SUM(off_peak_consumption_kwh) as off_peak_consumption'),
                    DB::raw('SUM(data_points_count) as data_points')
                )
                ->groupBy('month_start')
                ->get();
                
            foreach ($monthlyData as $data) {
                EnergyConsumptionAggregate::updateOrCreate(
                    [
                        'device_id' => $device->id,
                        'period_type' => 'monthly',
                        'period_start' => $data->month_start,
                    ],
                    [
                        'period_end' => date('Y-m-t', strtotime($data->month_start)) . ' 23:59:59',
                        'total_consumption_kwh' => $data->total_consumption,
                        'avg_power' => $data->avg_power,
                        'max_power' => $data->max_power,
                        'min_power' => $data->min_power,
                        'total_cost' => $data->total_cost,
                        'peak_consumption_kwh' => $data->peak_consumption,
                        'off_peak_consumption_kwh' => $data->off_peak_consumption,
                        'data_points_count' => $data->data_points,
                    ]
                );
            }
        }
    }

    protected function migrateToLongTermStorage($cutoffDate)
    {
        if (!config('influxdb.enabled')) return;
        
        $chunkSize = 1000;
        EnergyConsumption::where('created_at', '<', $cutoffDate)
            ->chunkById($chunkSize, function ($consumptions) {
                // InfluxDBService::bulkWrite($consumptions);
                // $consumptions->delete(); // Remover após migração
            });
    }
}
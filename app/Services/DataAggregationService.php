<?php

namespace App\Services;

use App\Models\Device;
use App\Models\EnergyConsumption;
use App\Models\EnergyConsumptionAggregate;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\InfluxDBService;

class DataAggregationService
{
    protected $influxDBService;

    public function __construct(InfluxDBService $influxDBService)
    {
        $this->influxDBService = $influxDBService;
    }
    public function generateHourlyAggregates(Device $device)
    {
        $lastHour = now()->subHour();

        $data = EnergyConsumption::where('device_id', $device->id)
            ->whereBetween('timestamp', [$lastHour, now()])
            ->select(
                DB::raw('SUM(consumption_kwh) as total_consumption'),
                DB::raw('AVG(instantaneous_power) as avg_power'),
                DB::raw('MAX(instantaneous_power) as max_power'),
                DB::raw('MIN(instantaneous_power) as min_power'),
                DB::raw('SUM(CASE WHEN is_peak_hour THEN consumption_kwh ELSE 0 END) as peak_consumption'),
                DB::raw('SUM(CASE WHEN NOT is_peak_hour THEN consumption_kwh ELSE 0 END) as off_peak_consumption'),
                DB::raw('COUNT(*) as data_points')
            )
            ->first();

        EnergyConsumptionAggregate::updateOrCreate(
            [
                'device_id' => $device->id,
                'period_type' => 'hourly',
                'period_start' => $lastHour,
            ],
            [
                'period_end' => now(),
                'total_consumption_kwh' => $data->total_consumption,
                'avg_power' => $data->avg_power,
                'max_power' => $data->max_power,
                'min_power' => $data->min_power,
                'peak_consumption_kwh' => $data->peak_consumption,
                'off_peak_consumption_kwh' => $data->off_peak_consumption,
                'data_points_count' => $data->data_points,
            ]
        );
    }

    public function generateDailyAggregates(Device $device)
    {
        $yesterday = now()->subDay();

        $hourlyAggregates = EnergyConsumptionAggregate::where('device_id', $device->id)
            ->where('period_type', 'hourly')
            ->where('period_start', '>=', $yesterday)
            ->get();

        $dailyData = [
            'total_consumption_kwh' => $hourlyAggregates->sum('total_consumption_kwh'),
            'avg_power' => $hourlyAggregates->avg('avg_power'),
            'max_power' => $hourlyAggregates->max('max_power'),
            'min_power' => $hourlyAggregates->min('min_power'),
            'peak_consumption_kwh' => $hourlyAggregates->sum('peak_consumption_kwh'),
            'off_peak_consumption_kwh' => $hourlyAggregates->sum('off_peak_consumption_kwh'),
            'data_points_count' => $hourlyAggregates->sum('data_points_count'),
        ];

        EnergyConsumptionAggregate::updateOrCreate(
            [
                'device_id' => $device->id,
                'period_type' => 'daily',
                'period_start' => $yesterday,
            ],
            array_merge(['period_end' => now()], $dailyData)
        );
    }

    public function migrateToLongTermStorage(Carbon $cutoff)
    {
        $devices = Device::all();

        foreach ($devices as $device) {
            $this->migrateDeviceData($device, $cutoff);
        }
    }

    protected function migrateDeviceData(Device $device, Carbon $cutoff)
    {
        $data = EnergyConsumption::where('device_id', $device->id)
            ->where('created_at', '<', $cutoff)
            ->get();

        foreach ($data as $record) {
            $this->influxDBService->writeEnergyData($device, [
                'consumption_kwh' => $record->consumption_kwh,
                'instantaneous_power' => $record->instantaneous_power,
                'voltage' => $record->voltage,
                'current' => $record->current,
                'power_factor' => $record->power_factor,
                'is_peak_hour' => $record->is_peak_hour,
                'timestamp' => $record->timestamp,
            ]);
        }

        // Remover dados antigos do MySQL
        EnergyConsumption::where('device_id', $device->id)
            ->where('created_at', '<', $cutoff)
            ->delete();
    }
}

<?php

namespace App\Jobs;

use App\Models\Device;
use App\Models\EnergyConsumption;
use App\Models\EnergyConsumptionAggregate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GenerateAggregatesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $periodType;
    protected $deviceId;

    public function __construct($periodType, $deviceId = null)
    {
        $this->periodType = $periodType;
        $this->deviceId = $deviceId;
    }

    public function handle()
    {
        $devices = $this->deviceId 
            ? Device::where('id', $this->deviceId)->get()
            : Device::all();

        foreach ($devices as $device) {
            $this->generateDeviceAggregates($device);
        }
    }

    protected function generateDeviceAggregates(Device $device)
    {
        $endDate = now();
        $startDate = match($this->periodType) {
            'hourly' => $endDate->copy()->subHour(),
            'daily' => $endDate->copy()->subDay(),
            'weekly' => $endDate->copy()->subWeek(),
            'monthly' => $endDate->copy()->subMonth(),
            'yearly' => $endDate->copy()->subYear(),
        };

        $query = EnergyConsumption::where('device_id', $device->id)
            ->whereBetween('timestamp', [$startDate, $endDate]);

        $results = $query->select(
                DB::raw('device_id'),
                DB::raw("DATE_FORMAT(timestamp, '{$this->getDateFormat()}') as period_start"),
                DB::raw('SUM(consumption_kwh) as total_consumption_kwh'),
                DB::raw('AVG(instantaneous_power) as avg_power'),
                DB::raw('MAX(instantaneous_power) as max_power'),
                DB::raw('MIN(instantaneous_power) as min_power'),
                DB::raw('SUM(cost_estimate) as total_cost'),
                DB::raw('SUM(CASE WHEN is_peak_hour THEN consumption_kwh ELSE 0 END) as peak_consumption_kwh'),
                DB::raw('SUM(CASE WHEN NOT is_peak_hour THEN consumption_kwh ELSE 0 END) as off_peak_consumption_kwh'),
                DB::raw('COUNT(*) as data_points_count')
            )
            ->groupBy('period_start')
            ->get();

        foreach ($results as $result) {
            EnergyConsumptionAggregate::updateOrCreate(
                [
                    'device_id' => $device->id,
                    'period_type' => $this->periodType,
                    'period_start' => Carbon::parse($result->period_start),
                ],
                [
                    'period_end' => Carbon::parse($result->period_start)->add(
                        $this->periodType === 'hourly' ? '1 hour' : 
                        ($this->periodType === 'daily' ? '1 day' : '1 month')
                    )->subSecond(),
                    'total_consumption_kwh' => $result->total_consumption_kwh,
                    'avg_power' => $result->avg_power,
                    'max_power' => $result->max_power,
                    'min_power' => $result->min_power,
                    'total_cost' => $result->total_cost,
                    'peak_consumption_kwh' => $result->peak_consumption_kwh,
                    'off_peak_consumption_kwh' => $result->off_peak_consumption_kwh,
                    'data_points_count' => $result->data_points_count,
                ]
            );
        }
    }

    protected function getDateFormat()
    {
        return match($this->periodType) {
            'hourly' => '%Y-%m-%d %H:00:00',
            'daily' => '%Y-%m-%d',
            'weekly' => '%Y-%u',
            'monthly' => '%Y-%m-01',
            'yearly' => '%Y-01-01',
        };
    }
}
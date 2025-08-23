<?php

namespace App\Services;

use App\Models\Device;
use App\Models\EnergyConsumption;
use App\Services\TimeSeriesService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EnergyAnalysisService
{
    protected $timeSeriesService;

    public function __construct(TimeSeriesService $timeSeriesService)
    {
        $this->timeSeriesService = $timeSeriesService;
    }

    public function calculateEnergyMetrics(Device $device, Carbon $start, Carbon $end): array
    {
        $data = EnergyConsumption::where('device_id', $device->id)
            ->whereBetween('timestamp', [$start, $end])
            ->select(
                DB::raw('SUM(consumption_kwh) as total_consumption'),
                DB::raw('AVG(instantaneous_power) as avg_power'),
                DB::raw('MAX(instantaneous_power) as max_power'),
                DB::raw('MIN(instantaneous_power) as min_power'),
                DB::raw('AVG(voltage) as avg_voltage'),
                DB::raw('AVG(power_factor) as avg_power_factor')
            )
            ->first();
        
        return [
            'total_consumption_kwh' => $data->total_consumption ?? 0,
            'avg_power' => $data->avg_power ?? 0,
            'max_power' => $data->max_power ?? 0,
            'min_power' => $data->min_power ?? 0,
            'avg_voltage' => $data->avg_voltage ?? 0,
            'avg_power_factor' => $data->avg_power_factor ?? 0,
        ];
    }

    public function detectConsumptionPatterns(Device $device, Carbon $start, Carbon $end): Collection
    {
        $consumptionData = EnergyConsumption::where('device_id', $device->id)
            ->whereBetween('timestamp', [$start, $end])
            ->select(
                DB::raw('HOUR(timestamp) as hour'),
                DB::raw('DAYOFWEEK(timestamp) as day_of_week'),
                DB::raw('AVG(consumption_kwh) as avg_consumption'),
                DB::raw('AVG(instantaneous_power) as avg_power')
            )
            ->groupBy('hour', 'day_of_week')
            ->get();
        
        return $consumptionData->map(function ($item) {
            return [
                'hour' => $item->hour,
                'day_of_week' => $item->day_of_week,
                'avg_consumption' => $item->avg_consumption,
                'avg_power' => $item->avg_power,
            ];
        });
    }

    public function detectAnomalies(Device $device, Carbon $start, Carbon $end): Collection
    {
        $baseline = $this->timeSeriesService->calculateBaseline($device, $start->copy()->subMonth(), $start);
        $currentData = EnergyConsumption::where('device_id', $device->id)
            ->whereBetween('timestamp', [$start, $end])
            ->get();
        
        return $currentData->filter(function ($dataPoint) use ($baseline) {
            $deviation = abs($dataPoint->instantaneous_power - $baseline['avg_power']);
            return $deviation > ($baseline['std_dev'] * 3); // 3 desvios padrão
        })->values();
    }

    public function generateEnergySavingInsights(Device $device): array
    {
        $dailyAvg = $this->calculateEnergyMetrics($device, now()->subWeek(), now())['avg_power'];
        $peakHours = $this->getPeakHours($device);
        
        $insights = [];
        
        if ($dailyAvg > ($device->rated_power * 0.8)) {
            $insights[] = "Seu dispositivo está operando próximo da capacidade máxima ({$device->rated_power}W). Considere otimizar o uso.";
        }
        
        if ($peakHours > 4) {
            $insights[] = "Você está usando {$peakHours} horas de pico por dia. Considere redistribuir o consumo para fora do horário de ponta.";
        }
        
        return $insights;
    }
    
    protected function getPeakHours(Device $device): float
    {
        return EnergyConsumption::where('device_id', $device->id)
            ->whereDate('timestamp', today())
            ->where('is_peak_hour', true)
            ->count() / 4; // 15min intervals
    }
}
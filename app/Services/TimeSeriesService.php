<?php

namespace App\Services;

use App\Models\Device;
use App\Models\EnergyConsumption;
use App\Services\InfluxDBService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TimeSeriesService
{
    protected $influxDBService;

    public function __construct(InfluxDBService $influxDBService)
    {
        $this->influxDBService = $influxDBService;
    }

    public function getTimeSeriesData(Device $device, Carbon $start, Carbon $end, string $interval): Collection
    {
        if (config('services.influxdb.enabled')) {
            return $this->getFromInfluxDB($device, $start, $end, $interval);
        }
        
        return $this->getFromDatabase($device, $start, $end, $interval);
    }

    protected function getFromInfluxDB(Device $device, Carbon $start, Carbon $end, string $interval): Collection
    {
        $query = <<<FLUX
        from(bucket: "{$this->influxDBService->getBucket()}")
          |> range(start: {$start->toIso8601String()}, stop: {$end->toIso8601String()})
          |> filter(fn: (r) => r["_measurement"] == "energy_consumption")
          |> filter(fn: (r) => r["device_id"] == "{$device->id}")
          |> aggregateWindow(every: {$interval}, fn: mean, createEmpty: false)
          |> yield(name: "mean")
        FLUX;
        
        $result = $this->influxDBService->queryEnergyData($query);
        return collect($result)->map(function ($point) {
            return [
                'time' => $point->getTime(),
                'value' => $point->getValue(),
            ];
        });
    }

    protected function getFromDatabase(Device $device, Carbon $start, Carbon $end, string $interval): Collection
    {
        $format = match ($interval) {
            '1h' => '%Y-%m-%d %H:00:00',
            '1d' => '%Y-%m-%d',
            default => '%Y-%m-%d %H:%i:00',
        };
        
        return EnergyConsumption::where('device_id', $device->id)
            ->whereBetween('timestamp', [$start, $end])
            ->select(
                DB::raw("DATE_FORMAT(timestamp, '{$format}') as time"),
                DB::raw('AVG(instantaneous_power) as value')
            )
            ->groupBy('time')
            ->orderBy('time')
            ->get();
    }

    public function detectTrends(Collection $data): string
    {
        if ($data->count() < 2) return 'stable';
        
        $first = $data->first()['value'];
        $last = $data->last()['value'];
        $change = ($last - $first) / $first * 100;
        
        if (abs($change) < 5) return 'stable';
        return $change > 0 ? 'increasing' : 'decreasing';
    }

    public function forecastConsumption(Device $device, int $periods = 7): array
    {
        // Implementar ARIMA, Prophet ou outro modelo de previsão
        // Retornar previsões para os próximos períodos
        return [];
    }

    public function calculateBaseline(Device $device, Carbon $start, Carbon $end): array
    {
        $data = $this->getTimeSeriesData($device, $start, $end, '1h');
        
        $values = $data->pluck('value')->toArray();
        
        return [
            'avg_power' => array_sum($values) / count($values),
            'std_dev' => $this->standardDeviation($values),
        ];
    }
    
    protected function standardDeviation(array $values): float
    {
        $avg = array_sum($values) / count($values);
        $variance = 0.0;
        
        foreach ($values as $value) {
            $variance += pow($value - $avg, 2);
        }
        
        return sqrt($variance / count($values));
    }
}
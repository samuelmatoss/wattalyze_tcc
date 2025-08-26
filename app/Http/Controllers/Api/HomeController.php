<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Models\Device;
use App\Models\Alert;
use App\Services\InfluxDBService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    /**
     * Get dashboard data for authenticated user
     */
    public function dashboard(InfluxDBService $influxService): JsonResponse
    {
        $user = auth()->user();

        $devices = Device::where('user_id', $user->id)
            ->with('deviceType')
            ->latest()
            ->limit(5)
            ->get();

        $alerts = Alert::where('user_id', $user->id)
            ->where('is_resolved', false)
            ->with('device')
            ->latest()
            ->limit(5)
            ->get();

        $dailyConsumption = [];
        $totalConsumptionValue = 0;

        foreach ($devices as $device) {
            $deviceId = $device->id;

            // Para cada tipo, pegamos os dados, se aplicável
            $types = ['energy', 'temperature', 'humidity'];
            $deviceData = [];

            foreach ($types as $type) {
                // Montar a measurementInfo para cada tipo (exceto se o device não for desse tipo, pode retornar vazio)
                $measurementInfo = $this->getMeasurementInfoByType($device, $type);
                if ($measurementInfo === null) continue; // não tem esse tipo para o device

                $data = $this->getDailyMeasurementData($influxService, $device, $measurementInfo);
                $deviceData[$type] = $data;

                // Somar consumo total só se for energia
                if ($type === 'energy') {
                    foreach ($data as $day) {
                        $totalConsumptionValue += $day['value'];
                    }
                }
            }

            $dailyConsumption[$deviceId] = $deviceData;
        }

        return response()->json([
            'devices' => $devices,
            'alerts' => $alerts,
            'daily_consumption' => $dailyConsumption,
            'total_consumption' => round($totalConsumptionValue, 3)
        ]);
    }

    // Private helper methods (unchanged from original)
    private function getDailyMeasurementData(InfluxDBService $influxService, Device $device, array $measurementInfo): array
    {
        $timezone = config('app.timezone', 'America/Sao_Paulo');
        $start = Carbon::now($timezone)->subDays(7)->startOfDay()->setTimezone('UTC')->toIso8601String();
        $stop = Carbon::now($timezone)->endOfDay()->setTimezone('UTC')->toIso8601String();
        $mac = $device->mac_address;

        // Determina a função de agregação baseada no tipo de dispositivo
        if (in_array($measurementInfo['measurement'], ['temperature', 'humidity'])) {
            $query = <<<FLUX
from(bucket: "{$influxService->getBucket()}")
  |> range(start: $start, stop: $stop)
  |> filter(fn: (r) => r._measurement == "{$measurementInfo['measurement']}")
  |> filter(fn: (r) => r.mac == "$mac")
  |> filter(fn: (r) => r._field == "{$measurementInfo['field']}")
  |> yield()
FLUX;
        } else {
            $query = <<<FLUX
from(bucket: "{$influxService->getBucket()}")
  |> range(start: $start, stop: $stop)
  |> filter(fn: (r) => r._measurement == "{$measurementInfo['measurement']}")
  |> filter(fn: (r) => r.mac == "$mac")
  |> filter(fn: (r) => r._field == "{$measurementInfo['field']}")
  |> yield()
FLUX;
        }

        try {
            $results = $influxService->queryEnergyData($query);
            $valuesPerDay = [];

            // Processa resultados com tratamento de valores
            foreach ($results as $row) {
                if (isset($row['value']) && $row['value'] !== null && $row['value'] !== '') {
                    $localDate = Carbon::parse($row['time'])->setTimezone($timezone)->format('Y-m-d');
                    $value = floatval($row['value']);

                    // Para sensores, sobrescreve o valor (média diária)
                    if (in_array($measurementInfo['measurement'], ['temperature', 'humidity'])) {
                        $valuesPerDay[$localDate] = $value;
                    }
                    // Para energia, acumula os valores
                    else {
                        if (isset($valuesPerDay[$localDate])) {
                            $valuesPerDay[$localDate] += $value;
                        } else {
                            $valuesPerDay[$localDate] = $value;
                        }
                    }
                }
            }

            // Garante dados para todos os últimos 7 dias
            $last7Days = $this->getLast7Days();
            $formattedData = [];

            foreach ($last7Days as $date) {
                $value = $valuesPerDay[$date] ?? 0;
                $precision = $measurementInfo['measurement'] === 'energy' ? 3 : 2;
                $formattedData[] = [
                    'date' => $date,
                    'value' => round($value, $precision)
                ];
            }

            return $formattedData;
        } catch (\Exception $e) {
            Log::error("Erro ao consultar dados diários para device {$device->id}: " . $e->getMessage());
            return $this->getFallbackData();
        }
    }

    private function getLast7Days(): array
    {
        $timezone = config('app.timezone', 'America/Sao_Paulo');
        $now = Carbon::now($timezone);
        $days = [];

        // Gera os últimos 7 dias (do mais antigo ao mais recente)
        for ($i = 6; $i >= 0; $i--) {
            $days[] = $now->copy()->subDays($i)->format('Y-m-d');
        }

        return $days;
    }

    private function getFallbackData(): array
    {
        $last7Days = $this->getLast7Days();
        $fallbackData = [];

        foreach ($last7Days as $date) {
            $fallbackData[] = ['date' => $date, 'value' => 0];
        }

        return $fallbackData;
    }

    private function getMeasurementInfoByType(Device $device, string $type): ?array
    {
        $typeName = strtolower($device->deviceType->name ?? '');

        return match ($type) {
            'temperature' => str_contains($typeName, 'temperature sensor') ? [
                'measurement' => 'temperature',
                'field' => 'temperature',
                'unit' => '°C',
            ] : null,
            'humidity' => str_contains($typeName, 'humidity sensor') ? [
                'measurement' => 'humidity',
                'field' => 'humidity',
                'unit' => '%',
            ] : null,
            'energy' => !str_contains($typeName, 'temperature sensor') && !str_contains($typeName, 'humidity sensor') ? [
                'measurement' => 'energy',
                'field' => 'consumption_kwh',
                'unit' => 'kWh',
            ] : null,
            default => null,
        };
    }
}
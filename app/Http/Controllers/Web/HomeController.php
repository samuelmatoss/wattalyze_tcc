<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Models\Device;
use App\Models\Alert;
use App\Services\InfluxDBService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    private const CACHE_TTL_DEVICES = 300; // 5 minutos
    private const CACHE_TTL_ALERTS = 60; // 1 minuto
    private const CACHE_TTL_DAILY_DATA = 600; // 10 minutos
    private const CACHE_TTL_SESSION_DATA = 1800; // 30 minutos

    public function dashboard(InfluxDBService $influxService)
    {
        $user = auth()->user();
        $sessionId = session()->getId();
        $userId = $user->id;

        // Cache key baseado no usuário e sessão
        $cacheKeyBase = "dashboard_data_{$userId}_{$sessionId}";

        // Cache dos devices do usuário
        $devices = $this->getCachedDevices($userId);

        // Cache dos alertas ativos
        $alerts = $this->getCachedAlerts($userId);

        // Cache dos dados de consumo diário
        $dailyConsumption = $this->getCachedDailyConsumption($influxService, $devices, $cacheKeyBase);

        // Cache do consumo total
        $totalConsumptionValue = $this->getCachedTotalConsumption($dailyConsumption, $cacheKeyBase);

        return view('dashboard.home', compact(
            'devices',
            'alerts',
            'dailyConsumption',
            'totalConsumptionValue'
        ));
    }

    private function getCachedDevices(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        $cacheKey = "user_devices_{$userId}";

        return Cache::remember($cacheKey, self::CACHE_TTL_DEVICES, function () use ($userId) {
            return Device::where('user_id', $userId)
                ->with('deviceType')
                ->latest()
                ->limit(5)
                ->get();
        });
    }

    private function getCachedAlerts(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        $cacheKey = "user_alerts_{$userId}";

        return Cache::remember($cacheKey, self::CACHE_TTL_ALERTS, function () use ($userId) {
            return Alert::where('user_id', $userId)
                ->where('is_resolved', false)
                ->with('device')
                ->latest()
                ->limit(5)
                ->get();
        });
    }

    private function getCachedDailyConsumption(InfluxDBService $influxService, $devices, string $cacheKeyBase): array
    {
        $cacheKey = "{$cacheKeyBase}_daily_consumption";

        return Cache::remember($cacheKey, self::CACHE_TTL_DAILY_DATA, function () use ($influxService, $devices) {
            $dailyConsumption = [];

            foreach ($devices as $device) {
                $deviceId = $device->id;
                $types = ['energy', 'temperature', 'humidity'];
                $deviceData = [];

                foreach ($types as $type) {
                    $measurementInfo = $this->getMeasurementInfoByType($device, $type);
                    if ($measurementInfo === null) continue;

                    // Cache individual para cada combinação device/type
                    $deviceTypeCacheKey = "device_data_{$deviceId}_{$type}_" . Carbon::now()->format('Y-m-d-H');
                    
                    $data = Cache::remember($deviceTypeCacheKey, self::CACHE_TTL_DAILY_DATA, function () use ($influxService, $device, $measurementInfo) {
                        return $this->getDailyMeasurementData($influxService, $device, $measurementInfo);
                    });

                    $deviceData[$type] = $data;
                }

                $dailyConsumption[$deviceId] = $deviceData;
            }

            return $dailyConsumption;
        });
    }

    private function getCachedTotalConsumption(array $dailyConsumption, string $cacheKeyBase): float
    {
        $cacheKey = "{$cacheKeyBase}_total_consumption";

        return Cache::remember($cacheKey, self::CACHE_TTL_SESSION_DATA, function () use ($dailyConsumption) {
            $totalConsumptionValue = 0;

            foreach ($dailyConsumption as $deviceData) {
                if (isset($deviceData['energy'])) {
                    foreach ($deviceData['energy'] as $day) {
                        $totalConsumptionValue += $day['value'];
                    }
                }
            }

            return $totalConsumptionValue;
        });
    }

    private function getDailyMeasurementData(InfluxDBService $influxService, Device $device, array $measurementInfo): array
    {
        $timezone = config('app.timezone', 'America/Sao_Paulo');
        $start = Carbon::now($timezone)->subDays(7)->startOfDay()->setTimezone('UTC')->toIso8601String();
        $stop = Carbon::now($timezone)->endOfDay()->setTimezone('UTC')->toIso8601String();
        $mac = $device->mac_address;

        // Cache específico para dados do InfluxDB baseado no dispositivo e período
        $dataCacheKey = "influx_data_{$device->id}_{$measurementInfo['measurement']}_" . Carbon::now()->format('Y-m-d-H');

        return Cache::remember($dataCacheKey, self::CACHE_TTL_DAILY_DATA, function () use ($influxService, $device, $measurementInfo, $start, $stop, $mac) {
            // Determina a função de agregação baseada no tipo de dispositivo
            if (in_array($measurementInfo['measurement'], ['temperature', 'humidity'])) {
                $query = <<<FLUX
from(bucket: "{$influxService->getBucket()}")
  |> range(start: $start, stop: $stop)
  |> filter(fn: (r) => r._measurement == "{$measurementInfo['measurement']}")
  |> filter(fn: (r) => r.mac == "$mac")
  |> filter(fn: (r) => r._field == "{$measurementInfo['field']}")
  |> aggregateWindow(every: 1d, fn: mean, createEmpty: false)
  |> yield()
FLUX;
            } else {
                $query = <<<FLUX
from(bucket: "{$influxService->getBucket()}")
  |> range(start: $start, stop: $stop)
  |> filter(fn: (r) => r._measurement == "{$measurementInfo['measurement']}")
  |> filter(fn: (r) => r.mac == "$mac")
  |> filter(fn: (r) => r._field == "{$measurementInfo['field']}")
  |> aggregateWindow(every: 1d, fn: sum, createEmpty: false)
  |> yield()
FLUX;
            }

            try {
                $results = $influxService->queryEnergyData($query);
                $valuesPerDay = [];

                // Processa resultados com tratamento de valores
                foreach ($results as $row) {
                    if (isset($row['value']) && $row['value'] !== null && $row['value'] !== '') {
                        $timezone = config('app.timezone', 'America/Sao_Paulo');
                        $localDate = Carbon::parse($row['time'])->setTimezone($timezone)->format('Y-m-d');
                        $value = floatval($row['value']);

                        // Para sensores, sobrescreve o valor (média diária já calculada pelo InfluxDB)
                        if (in_array($measurementInfo['measurement'], ['temperature', 'humidity'])) {
                            $valuesPerDay[$localDate] = $value;
                        }
                        // Para energia, usa soma já calculada pelo InfluxDB
                        else {
                            $valuesPerDay[$localDate] = $value;
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
        });
    }

    private function getLast7Days(): array
    {
        // Cache para os últimos 7 dias (muda apenas uma vez por dia)
        return Cache::remember('last_7_days_' . Carbon::now()->format('Y-m-d'), 86400, function () {
            $timezone = config('app.timezone', 'America/Sao_Paulo');
            $now = Carbon::now($timezone);
            $days = [];

            // Gera os últimos 7 dias (do mais antigo ao mais recente)
            for ($i = 6; $i >= 0; $i--) {
                $days[] = $now->copy()->subDays($i)->format('Y-m-d');
            }

            return $days;
        });
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
        // Cache para informações de medição por tipo de dispositivo
        $cacheKey = "measurement_info_{$device->id}_{$type}";

        return Cache::remember($cacheKey, 3600, function () use ($device, $type) {
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
        });
    }

    /**
     * Limpa cache específico do usuário (útil para quando dados são atualizados)
     */
    public function clearUserCache(int $userId): void
    {
        $patterns = [
            "user_devices_{$userId}",
            "user_alerts_{$userId}",
            "dashboard_data_{$userId}_*",
            "device_data_*",
            "influx_data_*",
        ];

        foreach ($patterns as $pattern) {
            if (str_contains($pattern, '*')) {
                // Para patterns com wildcard, você pode implementar uma lógica mais específica
                // dependendo do driver de cache que está usando
                continue;
            }
            Cache::forget($pattern);
        }
    }

    /**
     * Aquece o cache para o usuário atual
     */
    public function warmUpCache(InfluxDBService $influxService): void
    {
        $user = auth()->user();
        $sessionId = session()->getId();
        $cacheKeyBase = "dashboard_data_{$user->id}_{$sessionId}";

        // Pré-carrega dados no cache
        $this->getCachedDevices($user->id);
        $this->getCachedAlerts($user->id);
        
        $devices = $this->getCachedDevices($user->id);
        $this->getCachedDailyConsumption($influxService, $devices, $cacheKeyBase);
    }
}
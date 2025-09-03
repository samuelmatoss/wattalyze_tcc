<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Environment;
use App\Models\Device;
use App\Services\InfluxDBService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class EnvironmentController extends Controller
{
    private const CACHE_TTL_ENVIRONMENTS = 600; // 10 minutos
    private const CACHE_TTL_DEVICES = 300; // 5 minutos
    private const CACHE_TTL_DAILY_DATA = 600; // 10 minutos
    private const CACHE_TTL_PROCESSED_DATA = 1800; // 30 minutos
    private const CACHE_TTL_STATIC = 3600; // 1 hora

    public function index(InfluxDBService $influxService)
    {
        $userId = auth()->id();
        $sessionId = session()->getId();

        // Cache principal para dados do dashboard de ambientes
        $cacheKey = "environments_dashboard_{$userId}_{$sessionId}";

        $data = Cache::remember($cacheKey, self::CACHE_TTL_PROCESSED_DATA, function () use ($userId, $influxService) {
            $environments = $this->getCachedEnvironments($userId);
            $environmentDailyConsumption = $this->getCachedEnvironmentConsumption($influxService, $environments, $userId);

            return compact('environments', 'environmentDailyConsumption');
        });

        return view('environments.index', $data);
    }

    private function getCachedEnvironments(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        $cacheKey = "user_environments_{$userId}";

        return Cache::remember($cacheKey, self::CACHE_TTL_ENVIRONMENTS, function () use ($userId) {
            return Environment::with('devices.deviceType')
                ->where('user_id', $userId)
                ->get();
        });
    }

    private function getCachedEnvironmentConsumption(InfluxDBService $influxService, $environments, int $userId): array
    {
        $cacheKey = "environment_consumption_{$userId}_" . Carbon::now()->format('Y-m-d-H');

        return Cache::remember($cacheKey, self::CACHE_TTL_DAILY_DATA, function () use ($influxService, $environments) {
            $environmentDailyConsumption = [];

            foreach ($environments as $environment) {
                $environmentDailyConsumption[$environment->id] = $this->getCachedEnvironmentData(
                    $influxService, 
                    $environment
                );
            }

            return $environmentDailyConsumption;
        });
    }

    private function getCachedEnvironmentData(InfluxDBService $influxService, Environment $environment): array
    {
        $cacheKey = "environment_data_{$environment->id}_" . Carbon::now()->format('Y-m-d-H');

        return Cache::remember($cacheKey, self::CACHE_TTL_DAILY_DATA, function () use ($influxService, $environment) {
            $dailyTotals = $this->initializeDailyTotals();

            foreach ($environment->devices as $device) {
                $deviceData = $this->getCachedDeviceData($influxService, $device);
                $this->aggregateDeviceData($dailyTotals, $deviceData);
            }

            $this->calculateAverages($dailyTotals);
            return $this->formatDataForView($dailyTotals);
        });
    }

    private function getCachedDeviceData(InfluxDBService $influxService, Device $device): array
    {
        $cacheKey = "device_environment_data_{$device->id}_" . Carbon::now()->format('Y-m-d-H');

        return Cache::remember($cacheKey, self::CACHE_TTL_DAILY_DATA, function () use ($influxService, $device) {
            $types = ['energy', 'temperature', 'humidity'];
            $deviceData = [];

            foreach ($types as $type) {
                $measurementInfo = $this->getCachedMeasurementInfo($device, $type);
                if (!$measurementInfo) continue;

                $deviceData[$type] = $this->getDailyMeasurementData($influxService, $device, $measurementInfo);
            }

            return $deviceData;
        });
    }

    private function getCachedMeasurementInfo(Device $device, string $type): ?array
    {
        $cacheKey = "measurement_info_{$device->id}_{$type}";

        return Cache::remember($cacheKey, self::CACHE_TTL_STATIC, function () use ($device, $type) {
            return $this->getMeasurementInfoByType($device, $type);
        });
    }

    private function aggregateDeviceData(array &$dailyTotals, array $deviceData): void
    {
        foreach ($deviceData as $type => $data) {
            foreach ($data as $day) {
                $date = $day['date'];
                $value = $day['value'];

                switch ($type) {
                    case 'energy':
                        $dailyTotals[$date]['energy'] += $value;
                        break;
                    case 'temperature':
                        $dailyTotals[$date]['temperature'] += $value;
                        $dailyTotals[$date]['temp_count']++;
                        break;
                    case 'humidity':
                        $dailyTotals[$date]['humidity'] += $value;
                        $dailyTotals[$date]['humidity_count']++;
                        break;
                }
            }
        }
    }

    private function initializeDailyTotals(): array
    {
        $dailyTotals = [];
        foreach ($this->getCachedLast7Days() as $date) {
            $dailyTotals[$date] = [
                'energy' => 0,
                'temperature' => 0,
                'humidity' => 0,
                'temp_count' => 0,
                'humidity_count' => 0,
            ];
        }
        return $dailyTotals;
    }

    private function getCachedLast7Days(): array
    {
        $cacheKey = 'last_7_days_' . Carbon::now()->format('Y-m-d');
        
        return Cache::remember($cacheKey, 86400, function () {
            return $this->getLast7Days();
        });
    }

    private function calculateAverages(array &$dailyTotals): void
    {
        foreach ($dailyTotals as $date => &$values) {
            $values['temperature'] = $values['temp_count'] > 0
                ? round($values['temperature'] / $values['temp_count'], 2)
                : 0;
            $values['humidity'] = $values['humidity_count'] > 0
                ? round($values['humidity'] / $values['humidity_count'], 2)
                : 0;
            unset($values['temp_count'], $values['humidity_count']);
        }
    }

    private function formatDataForView(array $dailyTotals): array
    {
        $formatted = ['energy' => [], 'temperature' => [], 'humidity' => []];
        foreach ($dailyTotals as $date => $values) {
            foreach (['energy','temperature','humidity'] as $type) {
                $formatted[$type][] = ['date' => $date, 'value' => $values[$type]];
            }
        }
        return $formatted;
    }

    private function getLast7Days(): array
    {
        $timezone = config('app.timezone', 'America/Sao_Paulo');
        $now = Carbon::now($timezone);
        $days = [];
        for ($i = 6; $i >= 0; $i--) {
            $days[] = $now->copy()->subDays($i)->format('Y-m-d');
        }
        return $days;
    }

    private function getDailyMeasurementData(InfluxDBService $influxService, Device $device, array $measurementInfo): array
    {
        // Cache específico para dados do InfluxDB
        $influxCacheKey = "influx_environment_data_{$device->id}_{$measurementInfo['measurement']}_" . Carbon::now()->format('Y-m-d-H');

        return Cache::remember($influxCacheKey, self::CACHE_TTL_DAILY_DATA, function () use ($influxService, $device, $measurementInfo) {
            $timezone = config('app.timezone', 'America/Sao_Paulo');
            $start = Carbon::now($timezone)->subDays(7)->startOfDay()->setTimezone('UTC')->toIso8601String();
            $stop = Carbon::now($timezone)->endOfDay()->setTimezone('UTC')->toIso8601String();
            $mac = $device->mac_address;

            // Otimização: usar aggregateWindow para reduzir dados transferidos
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

                foreach ($results as $row) {
                    if (!isset($row['value']) || $row['value'] === null) continue;

                    $date = Carbon::parse($row['time'])->setTimezone($timezone)->format('Y-m-d');
                    $value = floatval($row['value']);

                    if (in_array($measurementInfo['measurement'], ['temperature','humidity'])) {
                        $valuesPerDay[$date] = $value; // Já é média do InfluxDB
                    } else {
                        $valuesPerDay[$date] = $value; // Já é soma do InfluxDB
                    }
                }

                $formatted = [];
                foreach ($this->getCachedLast7Days() as $date) {
                    $formatted[] = [
                        'date' => $date,
                        'value' => round($valuesPerDay[$date] ?? 0, $measurementInfo['measurement'] === 'energy' ? 3 : 2)
                    ];
                }

                return $formatted;
            } catch (\Exception $e) {
                Log::error("Erro ao consultar dados diários para device {$device->id}: " . $e->getMessage());
                return array_map(fn($d) => ['date'=>$d,'value'=>0], $this->getCachedLast7Days());
            }
        });
    }

    private function getMeasurementInfoByType(Device $device, string $type): ?array
    {
        $typeName = strtolower($device->deviceType->name ?? '');

        return match ($type) {
            'temperature' => str_contains($typeName,'temperature sensor') ? [
                'measurement' => 'temperature',
                'field' => 'temperature',
                'unit' => '°C',
            ] : null,
            'humidity' => str_contains($typeName,'humidity sensor') ? [
                'measurement' => 'humidity',
                'field' => 'humidity',
                'unit' => '%',
            ] : null,
            'energy' => !str_contains($typeName,'temperature sensor') && !str_contains($typeName,'humidity sensor') ? [
                'measurement' => 'energy',
                'field' => 'consumption_kwh',
                'unit' => 'kWh',
            ] : null,
            default => null,
        };
    }

    public function create()
    {
        return view('environments.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:residential,commercial,industrial,public',
            'description' => 'nullable|string|max:1000',
            'size_sqm' => 'nullable|numeric|min:0',
            'occupancy' => 'nullable|integer|min:0',
            'voltage_standard' => 'nullable|string|max:50',
            'tariff_type' => 'nullable|string|max:50',
            'energy_provider' => 'nullable|string|max:255',
            'installation_date' => 'nullable|date',
            'is_default' => 'nullable|boolean',
        ]);

        // Se marcou como padrão, desmarcar outros ambientes como padrão
        if (isset($validated['is_default']) && $validated['is_default']) {
            Environment::where('user_id', auth()->id())
                ->update(['is_default' => false]);
        }

        $environment = Environment::create(array_merge(
            $validated,
            ['user_id' => auth()->id()]
        ));

        // Limpar cache após criar ambiente
        $this->clearUserEnvironmentCache(auth()->id());

        return redirect()->route('environments.show', $environment->id)
            ->with('success', 'Ambiente criado com sucesso!');
    }

    public function edit(Environment $environment)
    {
        return view('environments.edit', compact('environment'));
    }

    public function update(Request $request, Environment $environment)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:residential,commercial,industrial,public',
            'description' => 'nullable|string|max:1000',
            'size_sqm' => 'nullable|numeric|min:0',
            'occupancy' => 'nullable|integer|min:0',
            'voltage_standard' => 'nullable|string|max:50',
            'tariff_type' => 'nullable|string|max:50',
            'energy_provider' => 'nullable|string|max:255',
            'installation_date' => 'nullable|date',
            'is_default' => 'nullable|boolean',
        ]);

        // Se marcou como padrão, desmarcar outros ambientes como padrão
        if (isset($validated['is_default']) && $validated['is_default']) {
            Environment::where('user_id', auth()->id())
                ->where('id', '!=', $environment->id)
                ->update(['is_default' => false]);
        }

        $environment->update($validated);

        // Limpar cache após atualizar ambiente
        $this->clearUserEnvironmentCache(auth()->id());
        $this->clearEnvironmentSpecificCache($environment->id);

        return redirect()->route('environments.show', $environment->id)
            ->with('success', 'Ambiente atualizado com sucesso!');
    }

    public function destroy(Environment $environment)
    {
        $userId = auth()->id();
        $environmentId = $environment->id;

        $environment->delete();

        // Limpar cache após deletar ambiente
        $this->clearUserEnvironmentCache($userId);
        $this->clearEnvironmentSpecificCache($environmentId);

        return redirect()->route('environments.index')
            ->with('success', 'Ambiente excluído com sucesso!');
    }

    /**
     * Limpa cache específico do usuário para ambientes
     */
    private function clearUserEnvironmentCache(int $userId): void
    {
        $sessionId = session()->getId();
        
        Cache::forget("user_environments_{$userId}");
        Cache::forget("environments_dashboard_{$userId}_{$sessionId}");
        Cache::forget("environment_consumption_{$userId}_" . Carbon::now()->format('Y-m-d-H'));
        
        // Limpar cache da hora anterior também
        Cache::forget("environment_consumption_{$userId}_" . Carbon::now()->subHour()->format('Y-m-d-H'));
    }

    /**
     * Limpa cache específico de um ambiente
     */
    private function clearEnvironmentSpecificCache(int $environmentId): void
    {
        $currentHour = Carbon::now()->format('Y-m-d-H');
        $previousHour = Carbon::now()->subHour()->format('Y-m-d-H');
        
        Cache::forget("environment_data_{$environmentId}_{$currentHour}");
        Cache::forget("environment_data_{$environmentId}_{$previousHour}");
    }

    /**
     * Aquece o cache para ambientes do usuário
     */
    public function warmUpCache(InfluxDBService $influxService): void
    {
        $userId = auth()->id();
        $sessionId = session()->getId();

        // Pré-carrega dados no cache
        $environments = $this->getCachedEnvironments($userId);
        $this->getCachedEnvironmentConsumption($influxService, $environments, $userId);
    }

    /**
     * Obtém estatísticas em cache para um ambiente específico
     */
    public function getCachedEnvironmentStats(int $environmentId, InfluxDBService $influxService): array
    {
        $cacheKey = "environment_stats_{$environmentId}_" . Carbon::now()->format('Y-m-d');

        return Cache::remember($cacheKey, self::CACHE_TTL_DAILY_DATA, function () use ($environmentId, $influxService) {
            $environment = Environment::with('devices.deviceType')->find($environmentId);
            if (!$environment) return [];

            $stats = [
                'total_devices' => $environment->devices->count(),
                'energy_devices' => 0,
                'sensor_devices' => 0,
                'total_energy_today' => 0,
                'avg_temperature' => 0,
                'avg_humidity' => 0,
            ];

            foreach ($environment->devices as $device) {
                $typeName = strtolower($device->deviceType->name ?? '');
                
                if (str_contains($typeName, 'temperature') || str_contains($typeName, 'humidity')) {
                    $stats['sensor_devices']++;
                } else {
                    $stats['energy_devices']++;
                }
            }

            return $stats;
        });
    }
}
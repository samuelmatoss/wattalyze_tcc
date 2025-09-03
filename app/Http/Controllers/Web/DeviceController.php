<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Environment;
use App\Services\InfluxDBService;
use App\Models\DeviceType;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeviceController extends Controller
{
    private const CACHE_TTL_DEVICES = 300; // 5 minutos
    private const CACHE_TTL_ALERTS = 60; // 1 minuto
    private const CACHE_TTL_DAILY_DATA = 600; // 10 minutos
    private const CACHE_TTL_POWER_DATA = 60; // 1 minuto para dados de potência
    private const CACHE_TTL_FORM_DATA = 1800; // 30 minutos

    public function index(InfluxDBService $influxService)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $userId = auth()->id();
        $sessionId = session()->getId();
        $cacheKeyBase = "user_devices_data_{$userId}_{$sessionId}";

        // Cache dos dados básicos
        $cachedData = Cache::remember("{$cacheKeyBase}_basic", self::CACHE_TTL_DEVICES, function () use ($userId) {
            return [
                'devices' => Device::where('user_id', $userId)
                    ->with(['deviceType:id,name', 'environment:id,name'])
                    ->select(['id', 'name', 'mac_address', 'status', 'device_type_id', 'environment_id', 'user_id'])
                    ->get(),
                'environments' => Environment::where('user_id', $userId)
                    ->select(['id', 'name'])
                    ->get(),
                'deviceTypes' => DeviceType::select(['id', 'name'])->get()
            ];
        });

        // Dados em tempo real com cache individual por dispositivo
        $deviceIds = $cachedData['devices']->pluck('id')->toArray();
        $influxData = $this->getCachedInfluxData($influxService, $deviceIds);
        $dailyConsumption = $this->getCachedDailyConsumption($influxService, $deviceIds);

        return view('devices.index', [
            'devices' => $cachedData['devices'],
            'environments' => $cachedData['environments'],
            'deviceTypes' => $cachedData['deviceTypes'],
            'influxData' => $influxData,
            'dailyConsumption' => $dailyConsumption
        ]);
    }

    private function getCachedInfluxData(InfluxDBService $influxService, array $deviceIds): array
    {
        $influxData = [];

        foreach ($deviceIds as $deviceId) {
            $device = Device::find($deviceId);
            if (!$device) {
                $influxData[$deviceId] = [
                    'value' => null,
                    'unit' => null,
                    'time' => null
                ];
                continue;
            }

            $measurementInfo = $this->getMeasurementInfo($device);
            $cacheKey = "device_measurement_{$deviceId}_" . Carbon::now()->format('Y-m-d-H-i');

            $influxData[$deviceId] = Cache::remember($cacheKey, self::CACHE_TTL_POWER_DATA, function () use ($influxService, $device, $measurementInfo) {
                return $this->getRealTimeMeasurementData($influxService, $device, $measurementInfo);
            });
        }

        return $influxData;
    }

    private function getRealTimeMeasurementData(InfluxDBService $influxService, Device $device, array $measurementInfo): array
    {
        $mac = $device->mac_address;
        $timeRange = $measurementInfo['measurement'] === 'energy' ? '-30m' : '-24h';

        $lastQuery = <<<FLUX
from(bucket: "{$influxService->getBucket()}")
|> range(start: {$timeRange})
|> filter(fn: (r) => r._measurement == "{$measurementInfo['measurement']}")
|> filter(fn: (r) => r.mac == "{$mac}")
|> filter(fn: (r) => r._field == "{$measurementInfo['field']}")
|> sort(columns: ["_time"], desc: true)
|> limit(n:1)
|> yield()
FLUX;

        try {
            $lastData = $influxService->queryEnergyData($lastQuery);

            Log::debug("LAST QUERY result for device {$device->id}", [
                'measurement' => $measurementInfo['measurement'],
                'field' => $measurementInfo['field'],
                'mac' => $mac,
                'timeRange' => $timeRange,
                'row_count' => count($lastData),
                'sample' => $lastData[0] ?? null
            ]);

            $row = $lastData[0] ?? null;
            $rawValue = null;
            $rawTime = null;

            if (is_array($row)) {
                if (array_key_exists('_value', $row)) {
                    $rawValue = $row['_value'];
                } elseif (array_key_exists('value', $row)) {
                    $rawValue = $row['value'];
                } elseif (array_key_exists('Value', $row)) {
                    $rawValue = $row['Value'];
                }

                $rawTime = $row['_time'] ?? ($row['time'] ?? null);
            }

            if ($rawValue === null || $rawValue === '') {
                $normalizedValue = $measurementInfo['measurement'] === 'energy' ? null : 0.0;
            } else {
                $raw = str_replace(',', '.', trim((string)$rawValue));
                $normalizedValue = is_numeric($raw) ? (float)round((float)$raw, $measurementInfo['measurement'] === 'energy' ? 3 : 2) : ($measurementInfo['measurement'] === 'energy' ? null : 0.0);
            }

            $normalizedTime = null;
            if ($rawTime) {
                try {
                    $dt = Carbon::parse($rawTime);
                    $dt->setTimezone(config('app.timezone', 'America/Sao_Paulo'));
                    $normalizedTime = $dt->toDateTimeString();
                } catch (\Exception $ex) {
                    Log::debug("Não foi possível parsear rawTime do Influx", ['device' => $device->id, 'rawTime' => $rawTime, 'err' => $ex->getMessage()]);
                    $normalizedTime = (string)$rawTime;
                }
            }

            return [
                'value' => $normalizedValue,
                'unit'  => $measurementInfo['unit'] ?? null,
                'time'  => $normalizedTime
            ];
        } catch (\Exception $e) {
            Log::error("Erro ao consultar dados em tempo real para device {$device->id}: " . $e->getMessage());
            return [
                'value' => $measurementInfo['measurement'] === 'energy' ? null : 0.0,
                'unit' => $measurementInfo['unit'] ?? null,
                'time' => null
            ];
        }
    }

    private function getCachedDailyConsumption(InfluxDBService $influxService, array $deviceIds): array
    {
        $dailyConsumption = [];

        foreach ($deviceIds as $deviceId) {
            $device = Device::find($deviceId);
            if (!$device) {
                $dailyConsumption[$deviceId] = [];
                continue;
            }

            $measurementInfo = $this->getMeasurementInfo($device);
            $cacheKey = "device_daily_consumption_{$deviceId}_" . Carbon::now()->format('Y-m-d');

            $dailyConsumption[$deviceId] = Cache::remember(
                $cacheKey,
                self::CACHE_TTL_DAILY_DATA,
                function () use ($influxService, $device, $measurementInfo) {
                    return $this->getDailyMeasurementData($influxService, $device, $measurementInfo);
                }
            );
        }

        return $dailyConsumption;
    }

    private function getDailyMeasurementData(InfluxDBService $influxService, Device $device, array $measurementInfo): array
    {
        $timezone = config('app.timezone', 'America/Sao_Paulo');
        $start = Carbon::now($timezone)->subDays(7)->startOfDay()->setTimezone('UTC')->toIso8601String();
        $stop = Carbon::now($timezone)->endOfDay()->setTimezone('UTC')->toIso8601String();
        $mac = $device->mac_address;

        // Determina a função de agregação baseada no tipo de medição
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
                if (isset($row['value']) && $row['value'] !== null && $row['value'] !== '') {
                    $localDate = Carbon::parse($row['time'])->setTimezone($timezone)->format('Y-m-d');
                    $value = floatval($row['value']);
                    
                    // Para sensores, usa o valor médio já calculado
                    if (in_array($measurementInfo['measurement'], ['temperature', 'humidity'])) {
                        $valuesPerDay[$localDate] = $value;
                    } else {
                        // Para energia, usa soma já calculada
                        $valuesPerDay[$localDate] = $value;
                    }
                }
            }

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
        $cacheKey = 'last_7_days_' . Carbon::now()->format('Y-m-d');
        
        return Cache::remember($cacheKey, 86400, function () {
            $timezone = config('app.timezone', 'America/Sao_Paulo');
            $now = Carbon::now($timezone);
            $days = [];

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

    public function create()
    {
        $userId = auth()->id();
        $cacheKey = "form_data_{$userId}";

        $formData = Cache::remember($cacheKey, self::CACHE_TTL_FORM_DATA, function () use ($userId) {
            return [
                'environments' => Environment::where('user_id', $userId)->select(['id', 'name'])->get(),
                'deviceTypes' => DeviceType::select(['id', 'name'])->get()
            ];
        });

        return view('devices.create', $formData);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mac_address' => 'required|string|size:17|unique:devices,mac_address',
            'serial_number' => 'nullable|string|max:255|unique:devices,serial_number',
            'model' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'firmware_version' => 'nullable|string|max:255',
            'status' => 'required|string|in:online,offline,maintenance',
            'location' => 'nullable|string|max:255',
            'installation_date' => 'nullable|date',
            'rated_power' => 'nullable|numeric|min:0',
            'rated_voltage' => 'nullable|numeric|min:0',
            'device_type_id' => 'nullable|exists:device_types,id',
            'environment_id' => 'nullable|exists:environments,id',
        ]);

        $validated['user_id'] = auth()->id();

        DB::transaction(function () use ($validated) {
            Device::create($validated);
        });

        $this->clearUserCache(auth()->id());

        return redirect()->route('devices.index')
            ->with('success', 'Dispositivo cadastrado com sucesso!');
    }

    public function edit(Device $device)
    {
        $userId = auth()->id();
        $cacheKey = "form_data_{$userId}";

        $formData = Cache::remember($cacheKey, self::CACHE_TTL_FORM_DATA, function () use ($userId) {
            return [
                'environments' => Environment::where('user_id', $userId)
                    ->select(['id', 'name'])
                    ->get(),
                'deviceTypes' => DeviceType::select(['id', 'name'])->get()
            ];
        });

        return view('devices.edit', array_merge(['device' => $device], $formData));
    }

    public function update(Request $request, Device $device)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mac_address' => 'required|string|size:17|unique:devices,mac_address,' . $device->id,
            'serial_number' => 'nullable|string|max:255|unique:devices,serial_number,' . $device->id,
            'model' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'firmware_version' => 'nullable|string|max:255',
            'status' => 'required|string|in:online,offline,maintenance',
            'location' => 'nullable|string|max:255',
            'installation_date' => 'nullable|date',
            'rated_power' => 'nullable|numeric|min:0',
            'rated_voltage' => 'nullable|numeric|min:0',
            'device_type_id' => 'nullable|exists:device_types,id',
            'environment_id' => 'nullable|exists:environments,id',
        ]);

        DB::transaction(function () use ($device, $validated) {
            $device->update($validated);
        });

        $this->clearDeviceCache($device->id);
        $this->clearUserCache($device->user_id);

        return redirect()->route('devices.show', $device->id)
            ->with('success', 'Dispositivo atualizado com sucesso!');
    }

    public function diagnostics(Device $device)
    {
        $this->authorize('view', $device);

        $cacheKey = "device_diagnostics_{$device->id}";
        $diagnosticsData = Cache::remember($cacheKey, 120, function () use ($device) {
            return [
                'lastSeen' => $device->last_seen_at,
                'status' => $device->status,
                'consumptionData' => $device->energyConsumptions()
                    ->select(['timestamp', 'consumption_kwh', 'instantaneous_power', 'voltage', 'current'])
                    ->orderBy('timestamp', 'desc')
                    ->limit(50)
                    ->get()
            ];
        });

        return view('devices.diagnostics', array_merge(
            ['device' => $device],
            $diagnosticsData
        ));
    }

    /**
     * Limpa cache relacionado ao usuário
     */
    private function clearUserCache(int $userId): void
    {
        $patterns = [
            "user_devices_data_{$userId}_*",
            "form_data_{$userId}",
            "device_measurement_*",
            "device_daily_consumption_*",
            "device_diagnostics_*"
        ];

        foreach ($patterns as $pattern) {
            if (str_contains($pattern, '*')) {
                // Limpar todos os caches que correspondem ao padrão
                // Esta implementação pode variar dependendo do driver de cache
                // Aqui é um exemplo genérico
                Cache::flush();
            } else {
                Cache::forget($pattern);
            }
        }
    }

    private function clearDeviceCache(int $deviceId): void
    {
        // Limpa cache de medição em tempo real
        Cache::forget("device_measurement_{$deviceId}");

        // Limpa cache de consumo histórico
        Cache::forget("device_consumption_{$deviceId}");

        // Limpa cache de diagnósticos
        Cache::forget("device_diagnostics_{$deviceId}");

        // Limpa cache de consumo diário (últimos 10 dias + próximos 3 dias)
        for ($i = -10; $i <= 3; $i++) {
            $date = Carbon::now()->addDays($i)->format('Y-m-d');
            Cache::forget("device_daily_consumption_{$deviceId}_{$date}");
        }
    }

    private function getMeasurementInfo(Device $device): array
    {
        $cacheKey = "measurement_info_{$device->id}";
        
        return Cache::remember($cacheKey, 3600, function () use ($device) {
            $typeName = strtolower($device->deviceType->name ?? '');

            return match (true) {
                str_contains($typeName, 'temperature sensor') => [
                    'measurement' => 'temperature',
                    'field' => 'temperature',
                    'unit' => '°C'
                ],
                str_contains($typeName, 'humidity sensor') => [
                    'measurement' => 'humidity',
                    'field' => 'humidity',
                    'unit' => '%'
                ],
                default => [
                    'measurement' => 'energy',
                    'field' => 'consumption_kwh',
                    'unit' => 'kWh'
                ]
            };
        });
    }
}
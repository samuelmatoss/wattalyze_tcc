<?php

namespace App\Http\Controllers\Api;

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
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ApiDeviceController extends Controller
{
    private const CACHE_TTL = 300; // 5 minutos
    private const POWER_CACHE_TTL = 60; // 1 minuto para dados de potência

    /**
     * Get all devices for authenticated user
     */
    public function index(InfluxDBService $influxService): JsonResponse
    {
        $userId = auth()->id();
        $cacheKey = "user_devices_data_{$userId}";

        // Cache completo dos dados básicos por 5 minutos
        $cachedData = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($userId) {
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

        // Dados em tempo real (cache menor)
        $deviceIds = $cachedData['devices']->pluck('id')->toArray();
        $influxData = $this->getCachedInfluxData($influxService, $deviceIds);
        $dailyConsumption = $this->getCachedDailyConsumption($influxService, $deviceIds);

        return response()->json([
            'devices' => $cachedData['devices'],
            'environments' => $cachedData['environments'],
            'device_types' => $cachedData['deviceTypes'],
            'influx_data' => $influxData,
            'daily_consumption' => $dailyConsumption
        ]);
    }

    /**
     * Get form data for creating a new device
     */
    public function create(): JsonResponse
    {
        $userId = auth()->id();

        $environments = Environment::where('user_id', $userId)->select(['id', 'name'])->get();

        // Cache para dados de formulário
        $formData = Cache::remember("form_data_{$userId}", 1800, function () {
            return [
                'device_types' => DeviceType::select(['id', 'name'])->get()
            ];
        });

        return response()->json([
            'environments' => $environments,
            'device_types' => $formData['device_types'],
        ]);
    }

    /**
     * Store a new device
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();
        $validated['user_id'] = auth()->id();

        DB::transaction(function () use ($validated) {
            Device::create($validated);
        });

        // Limpa cache relacionado
        $this->clearUserCache(auth()->id());

        return response()->json([
            'message' => 'Dispositivo cadastrado com sucesso!'
        ], 201);
    }

    /**
     * Get a single device
     */
    public function show(Device $device): JsonResponse
    {
        // Check authorization
        if ($device->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Cache para dados de consumo do dispositivo
        $cacheKey = "device_consumption_{$device->id}";
        $consumption = Cache::remember($cacheKey, 300, function () use ($device) {
            return $device->energyConsumptions()
                ->select(['timestamp', 'consumption_kwh', 'instantaneous_power'])
                ->orderBy('timestamp', 'desc')
                ->limit(100)
                ->get();
        });

        return response()->json([
            'device' => $device->load(['deviceType', 'environment']),
            'consumption' => $consumption
        ]);
    }

    /**
     * Get device data for editing
     */
    public function edit(Device $device): JsonResponse
    {
        // Check authorization
        if ($device->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $userId = auth()->id();
        $formData = Cache::remember("form_data_{$userId}", 1800, function () use ($userId) {
            return [
                'environments' => Environment::where('user_id', $userId)
                    ->select(['id', 'name'])
                    ->get(),
                'device_types' => DeviceType::select(['id', 'name'])->get()
            ];
        });

        return response()->json(array_merge([
            'device' => $device
        ], $formData));
    }

    /**
     * Update a device
     */
    public function update(Request $request, Device $device): JsonResponse
    {
        // Check authorization
        if ($device->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        DB::transaction(function () use ($device, $validated) {
            $device->update($validated);
        });

        // Limpa cache específico do dispositivo e do usuário
        $this->clearDeviceCache($device->id);
        $this->clearUserCache($device->user_id);

        return response()->json([
            'message' => 'Dispositivo atualizado com sucesso!',
            'device' => $device->fresh()
        ]);
    }

    /**
     * Get device diagnostics
     */
    public function diagnostics(Device $device): JsonResponse
    {
        // Check authorization
        if ($device->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $cacheKey = "device_diagnostics_{$device->id}";
        $diagnosticsData = Cache::remember($cacheKey, 120, function () use ($device) {
            return [
                'last_seen' => $device->last_seen_at,
                'status' => $device->status,
                'consumption_data' => $device->energyConsumptions()
                    ->select(['timestamp', 'consumption_kwh', 'instantaneous_power', 'voltage', 'current'])
                    ->orderBy('timestamp', 'desc')
                    ->limit(50)
                    ->get()
            ];
        });

        return response()->json(array_merge(
            ['device' => $device],
            $diagnosticsData
        ));
    }

    /**
     * Debug device data
     */
    public function debug(Device $device, InfluxDBService $influxService): JsonResponse
    {
        // Check authorization
        if ($device->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Limpar todo o cache relacionado
        $this->clearDeviceCache($device->id);
        $this->clearUserCache($device->user_id);

        // Fazer query direta para verificar dados
        $measurementInfo = $this->getMeasurementInfo($device);
        $mac = $device->mac_address;

        $debugQuery = <<<FLUX
        from(bucket: "{$influxService->getBucket()}")
        |> range(start: -7d)
        |> filter(fn: (r) => r._measurement == "{$measurementInfo['measurement']}")
        |> filter(fn: (r) => r.mac == "{$mac}")
        |> filter(fn: (r) => r._field == "{$measurementInfo['field']}")
        |> limit(n: 20)
        FLUX;

        try {
            $rawData = $influxService->queryEnergyData($debugQuery);

            Log::info("DEBUG - Raw data for device {$device->id}:", [
                'measurement_info' => $measurementInfo,
                'mac_address' => $mac,
                'raw_data_count' => count($rawData),
                'sample_data' => array_slice($rawData, 0, 5)
            ]);

            return response()->json([
                'device_id' => $device->id,
                'measurement_info' => $measurementInfo,
                'raw_data_count' => count($rawData),
                'sample_data' => array_slice($rawData, 0, 10)
            ]);
        } catch (\Exception $e) {
            Log::error("Erro no debug do device {$device->id}: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete a device
     */
    public function destroy(Device $device): JsonResponse
    {
        // Check authorization
        if ($device->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Limpa cache antes de deletar
        $this->clearDeviceCache($device->id);
        $this->clearUserCache($device->user_id);

        $device->delete();

        return response()->json([
            'message' => 'Dispositivo excluído com sucesso!'
        ]);
    }

    private function getCachedInfluxData(InfluxDBService $influxService, array $deviceIds): array
    {
        $influxData = [];

        foreach ($deviceIds as $deviceId) {
            $device = Device::find($deviceId);
            if (!$device) {
                // dispositivo removido — devolve valores neutros
                $influxData[$deviceId] = [
                    'value' => null,
                    'unit' => null,
                    'time' => null
                ];
                continue;
            }

            $measurementInfo = $this->getMeasurementInfo($device);
            $cacheKey = "device_measurement_{$deviceId}";

            $influxData[$deviceId] = Cache::remember($cacheKey, self::POWER_CACHE_TTL, function () use ($influxService, $device, $measurementInfo) {
                $mac = $device->mac_address;

                // time range: energia precisa de janela curta; sensores podem ter janela maior
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
                        // prioriza _value (Flux), depois value (possíveis libs)
                        if (array_key_exists('_value', $row)) {
                            $rawValue = $row['_value'];
                        } elseif (array_key_exists('value', $row)) {
                            $rawValue = $row['value'];
                        } elseif (array_key_exists('Value', $row)) {
                            $rawValue = $row['Value'];
                        }

                        // tempo: _time (Flux) ou time
                        $rawTime = $row['_time'] ?? ($row['time'] ?? null);
                    }

                    // normaliza valor
                    if ($rawValue === null || $rawValue === '') {
                        // para energia, mantemos null quando não há leitura; para sensores, devolvemos 0.0
                        $normalizedValue = $measurementInfo['measurement'] === 'energy' ? null : 0.0;
                    } else {
                        $raw = str_replace(',', '.', trim((string)$rawValue));
                        $normalizedValue = is_numeric($raw) ? (float)round((float)$raw, $measurementInfo['measurement'] === 'energy' ? 3 : 2) : ($measurementInfo['measurement'] === 'energy' ? null : 0.0);
                    }

                    // normaliza tempo para timezone da app (string legível) — fallback para raw
                    $normalizedTime = null;
                    if ($rawTime) {
                        try {
                            $dt = Carbon::parse($rawTime);
                            $dt->setTimezone(config('app.timezone', 'America/Sao_Paulo'));
                            $normalizedTime = $dt->toDateTimeString(); // "YYYY-MM-DD HH:MM:SS"
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
            });
        }

        return $influxData;
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
                self::CACHE_TTL,
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

        // Flux para temperatura e umidade sem agregação, pega os dados brutos
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

            foreach ($results as $row) {
                if (isset($row['value']) && $row['value'] !== null && $row['value'] !== '') {
                    $localDate = Carbon::parse($row['time'])->setTimezone($timezone)->format('Y-m-d');
                    $value = floatval($row['value']);

                    if (in_array($measurementInfo['measurement'], ['temperature', 'humidity'])) {
                        // Último valor do dia sobrescreve o anterior
                        $valuesPerDay[$localDate] = $value;
                    } else {
                        if (isset($valuesPerDay[$localDate])) {
                            $valuesPerDay[$localDate] += $value;
                        } else {
                            $valuesPerDay[$localDate] = $value;
                        }
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
        $timezone = config('app.timezone', 'America/Sao_Paulo');
        $now = Carbon::now($timezone);
        $days = [];

        // Começa de 6 dias atrás até hoje (7 dias no total)
        for ($i = 6; $i >= 0; $i--) {
            $days[] = $now->copy()->subDays($i)->format('Y-m-d');
        }

        return $days;
    }

    /**
     * Limpa cache relacionado ao usuário
     */
    private function clearUserCache(int $userId): void
    {
        Cache::forget("user_devices_data_{$userId}");
        Cache::forget("form_data_{$userId}");
    }

    private function clearDeviceCache(int $deviceId): void
    {
        // Cache de medição em tempo real (chave correta usada em getCachedInfluxData)
        Cache::forget("device_measurement_{$deviceId}");

        // Cache de consumo histórico
        Cache::forget("device_consumption_{$deviceId}");

        // Cache de diagnósticos
        Cache::forget("device_diagnostics_{$deviceId}");

        // Limpa cache de consumo diário (últimos 10 dias + próximos 3 dias)
        for ($i = -10; $i <= 3; $i++) {
            $date = Carbon::now()->addDays($i)->format('Y-m-d');
            Cache::forget("device_daily_consumption_{$deviceId}_{$date}");
        }
    }

    private function getMeasurementInfo(Device $device): array
    {
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
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Environment;
use App\Models\Device;
use App\Services\InfluxDBService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class EnvironmentController extends Controller
{
     private const CACHE_TTL = 300; // 5 minutos

    /**
     * Get all environments with their consumption data
     */
    public function index(InfluxDBService $influxService): JsonResponse
    {
        $userId = auth()->id();

        $environments = Environment::with('devices.deviceType')
            ->where('user_id', $userId)
            ->get();

        $environmentDailyConsumption = [];

        foreach ($environments as $environment) {
            $dailyTotals = $this->initializeDailyTotals();

            foreach ($environment->devices as $device) {
                $types = ['energy', 'temperature', 'humidity'];
                foreach ($types as $type) {
                    $measurementInfo = $this->getMeasurementInfoByType($device, $type);
                    if (!$measurementInfo) continue;

                    $data = $this->getDailyMeasurementData($influxService, $device, $measurementInfo);

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

            $this->calculateAverages($dailyTotals);
            $environmentDailyConsumption[$environment->id] = $this->formatDataForView($dailyTotals);
        }

        return response()->json([
            'environments' => $environments,
            'environment_daily_consumption' => $environmentDailyConsumption
        ]);
    }

    /**
     * Create a new environment
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        // If marked as default, unmark other environments as default
        if (isset($validated['is_default']) && $validated['is_default']) {
            Environment::where('user_id', auth()->id())
                ->update(['is_default' => false]);
        }

        $environment = Environment::create(array_merge(
            $validated,
            ['user_id' => auth()->id()]
        ));

        return response()->json([
            'message' => 'Ambiente criado com sucesso!',
            'environment' => $environment
        ], 201);
    }

    /**
     * Get a single environment
     */
    public function show(Environment $environment): JsonResponse
    {
        // Check authorization
        if ($environment->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'environment' => $environment->load('devices.deviceType')
        ]);
    }

    /**
     * Update an environment
     */
    public function update(Request $request, Environment $environment): JsonResponse
    {
        // Check authorization
        if ($environment->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        // If marked as default, unmark other environments as default
        if (isset($validated['is_default']) && $validated['is_default']) {
            Environment::where('user_id', auth()->id())
                ->where('id', '!=', $environment->id)
                ->update(['is_default' => false]);
        }

        $environment->update($validated);

        return response()->json([
            'message' => 'Ambiente atualizado com sucesso!',
            'environment' => $environment
        ]);
    }

    /**
     * Delete an environment
     */
    public function destroy(Environment $environment): JsonResponse
    {
        // Check authorization
        if ($environment->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $environment->delete();

        return response()->json(['message' => 'Ambiente excluído com sucesso!']);
    }

    /**
     * Get environment consumption data
     */
    public function consumption(Environment $environment, InfluxDBService $influxService): JsonResponse
    {
        // Check authorization
        if ($environment->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $dailyTotals = $this->initializeDailyTotals();

        foreach ($environment->devices as $device) {
            $types = ['energy', 'temperature', 'humidity'];
            foreach ($types as $type) {
                $measurementInfo = $this->getMeasurementInfoByType($device, $type);
                if (!$measurementInfo) continue;

                $data = $this->getDailyMeasurementData($influxService, $device, $measurementInfo);

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

        $this->calculateAverages($dailyTotals);
        $consumptionData = $this->formatDataForView($dailyTotals);

        return response()->json([
            'environment' => $environment,
            'consumption_data' => $consumptionData
        ]);
    }

    // Private helper methods (unchanged from original)
    private function initializeDailyTotals(): array
    {
        $dailyTotals = [];
        foreach ($this->getLast7Days() as $date) {
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
        $timezone = config('app.timezone', 'America/Sao_Paulo');
        $start = Carbon::now($timezone)->subDays(7)->startOfDay()->setTimezone('UTC')->toIso8601String();
        $stop = Carbon::now($timezone)->endOfDay()->setTimezone('UTC')->toIso8601String();
        $mac = $device->mac_address;

        $query = <<<FLUX
from(bucket: "{$influxService->getBucket()}")
  |> range(start: $start, stop: $stop)
  |> filter(fn: (r) => r._measurement == "{$measurementInfo['measurement']}")
  |> filter(fn: (r) => r.mac == "$mac")
  |> filter(fn: (r) => r._field == "{$measurementInfo['field']}")
  |> yield()
FLUX;

        try {
            $results = $influxService->queryEnergyData($query);
            $valuesPerDay = [];

            foreach ($results as $row) {
                if (!isset($row['value']) || $row['value'] === null) continue;

                $date = Carbon::parse($row['time'])->setTimezone($timezone)->format('Y-m-d');
                $value = floatval($row['value']);

                if (in_array($measurementInfo['measurement'], ['temperature','humidity'])) {
                    $valuesPerDay[$date] = $value;
                } else {
                    $valuesPerDay[$date] = ($valuesPerDay[$date] ?? 0) + $value;
                }
            }

            $formatted = [];
            foreach ($this->getLast7Days() as $date) {
                $formatted[] = [
                    'date' => $date,
                    'value' => round($valuesPerDay[$date] ?? 0, $measurementInfo['measurement'] === 'energy' ? 3 : 2)
                ];
            }

            return $formatted;
        } catch (\Exception $e) {
            Log::error("Erro ao consultar dados diários para device {$device->id}: " . $e->getMessage());
            return array_map(fn($d) => ['date'=>$d,'value'=>0], $this->getLast7Days());
        }
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
    
}

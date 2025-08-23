<?php
namespace App\Services;

use App\Models\Device;
use Carbon\Carbon;
use InfluxDB2\Client;
use InfluxDB2\Model\WritePrecision;
use InfluxDB2\Point;

class InfluxDBService
{
    protected $client;
    protected $bucket;
    protected $org;

    public function __construct()
    {
        $this->client = new Client([
            'url' => config('services.influxdb.url'),
            'token' => config('services.influxdb.token'),
        ]);

        $this->bucket = config('services.influxdb.bucket');
        $this->org = config('services.influxdb.org');
    }

    public function writeEnergyData(Device $device, array $data)
    {
        $timestamp = $data['timestamp'];
        if ($timestamp instanceof \DateTimeInterface) {
            $timestamp = $timestamp->getTimestamp();
        }

        $point = Point::measurement('energy_consumption')
            ->addTag('device_id', (string)$device->id)
            ->addTag('device_type', $device->deviceType->name ?? 'unknown')
            ->addTag('environment_id', (string)$device->environment_id)
            ->addField('consumption_kwh', (float)$data['consumption_kwh'])
            ->addField('instantaneous_power', (float)$data['instantaneous_power'])
            ->addField('voltage', (float)$data['voltage'])
            ->addField('current', (float)$data['current'])
            ->addField('power_factor', (float)$data['power_factor'])
            ->addField('is_peak_hour', (bool)$data['is_peak_hour'])
            ->time($timestamp, WritePrecision::S);

        $this->client->createWriteApi()->write($point, $this->bucket, $this->org);
    }

    public function queryEnergyData(string $query): array
    {
        try {
            $queryApi = $this->client->createQueryApi();
            $tables = $queryApi->query($query, $this->org);

            $results = [];

            foreach ($tables as $table) {
                foreach ($table->records as $record) {
                    $time = $record->getTime();

                    $timeString = null;
                    if ($time instanceof \DateTimeInterface) {
                        $timeString = $time->format('c');
                    } elseif (is_string($time)) {
                        $timeString = $time;
                    } elseif (is_int($time)) {
                        $timeString = date('c', $time);
                    }

                    $results[] = [
                        'time' => $timeString,
                        'measurement' => $record->getMeasurement(),
                        'field' => $record->getField(),
                        'value' => $record->getValue(),
                        'device_id' => $record->values['device_id'] ?? null,
                    ];
                }
            }

            return $results;
        } catch (\Exception $e) {
            // Você pode usar Log::error($e->getMessage());
            return [];
        }
    }

    public function getDailyConsumption(Device $device, int $days = 7): array
    {
        $query = sprintf('
            from(bucket: "%s")
            |> range(start: -%dd, stop: now())
            |> filter(fn: (r) => r._measurement == "energy_consumption")
            |> filter(fn: (r) => r.device_id == "%s")
            |> filter(fn: (r) => r._field == "consumption_kwh")
            |> aggregateWindow(every: 1d, fn: sum, createEmpty: false)
            |> sort(columns: ["_time"])
        ', $this->bucket, $days, $device->id);

        $results = $this->queryEnergyData($query);

        return array_map(function ($row) {
            $date = Carbon::parse($row['time'])->format('Y-m-d');
            return [
                'date' => $date,
                'value' => floatval($row['value'] ?? 0),
            ];
        }, $results);
    }

    public function getLastInstantaneousPower(Device $device): ?array
    {
        $query = sprintf('
            from(bucket: "%s")
            |> range(start: -1h)
            |> filter(fn: (r) => r._measurement == "energy_consumption")
            |> filter(fn: (r) => r.device_id == "%s")
            |> filter(fn: (r) => r._field == "instantaneous_power")
            |> last()
        ', $this->bucket, $device->id);

        $results = $this->queryEnergyData($query);

        if (empty($results)) {
            return null;
        }

        return [
            'instantaneous_power' => floatval($results[0]['value'] ?? 0),
            'time' => $results[0]['time']
        ];
    }

    public function insertTestData(Device $device): void
    {
        $now = Carbon::now();

        for ($i = 6; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);

            for ($hour = 0; $hour < 24; $hour++) {
                $timestamp = $date->copy()->addHours($hour);

                $data = [
                    'timestamp' => $timestamp,
                    'consumption_kwh' => rand(10, 100) / 100,
                    'instantaneous_power' => rand(500, 3000),
                    'voltage' => rand(210, 230),
                    'current' => rand(2, 15),
                    'power_factor' => rand(80, 100) / 100,
                    'is_peak_hour' => $hour >= 18 && $hour <= 21,
                ];

                $this->writeEnergyData($device, $data);
            }
        }
    }

    public function getBucket(): string
    {
        return $this->bucket;
    }

    public function getOrganization(): string
    {
        return $this->org;
    }
}

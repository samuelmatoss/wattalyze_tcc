<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use InfluxDB2\Client;
use InfluxDB2\Model\WritePrecision;
use InfluxDB2\Point;
use App\Models\Device;

class CreateDefaultMeasurements extends Command
{
    protected $signature = 'influx:create-device-measurements';
    protected $description = 'Cria measurements no InfluxDB com base nos dispositivos e seus MACs';

    public function handle()
    {
        $client = new Client([
            'url' => config('influxdb.url'),
            'token' => config('influxdb.token'),
            'org' => config('influxdb.org'),
            'bucket' => config('influxdb.bucket'),
            'precision' => WritePrecision::S,
        ]);

        $writeApi = $client->createWriteApi();

        $devices = Device::all();

        foreach ($devices as $device) {
            $mac = $device->mac_address ?? 'unknown_mac';
            $environmentId = (string) ($device->environment_id ?? 'unknown_environment');

            // Energy
            $pointEnergy = Point::measurement('energy')
                ->addTag('device_id', (string)$device->id)
                ->addTag('mac', $mac)
                ->addTag('environment_id', $environmentId)
                ->addField('instantaneous_power', 0.0)
                ->addField('consumption_kwh', 0.0)
                ->addField('voltage', 0.0)
                ->addField('current', 0.0)
                ->time(time(), WritePrecision::S);

            $writeApi->write($pointEnergy);

            // Temperature
            $pointTemp = Point::measurement('temperature')
                ->addTag('device_id', (string)$device->id)
                ->addTag('mac', $mac)
                ->addTag('environment_id', $environmentId)
                ->addField('temperature', 0.0)
                ->time(time(), WritePrecision::S);

            $writeApi->write($pointTemp);

            // Humidity
            $pointHumidity = Point::measurement('humidity')
                ->addTag('device_id', (string)$device->id)
                ->addTag('mac', $mac)
                ->addTag('environment_id', $environmentId)
                ->addField('humidity', 0.0)
                ->time(time(), WritePrecision::S);

            $writeApi->write($pointHumidity);

            $this->info("Measurements energy, temperature e humidity criadas para device ID {$device->id} ({$device->name})");
        }

        $client->close();
        $this->info("Todas as measurements foram criadas com sucesso!");
    }
}

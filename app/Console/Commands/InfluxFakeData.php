<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use InfluxDB2\Client;
use InfluxDB2\Model\WritePrecision;
use InfluxDB2\Point;
use Carbon\Carbon;

class InfluxFakeData extends Command
{
    protected $signature = 'influx:fake-data {count=10}';
    protected $description = 'Insere dados falsos no InfluxDB Cloud';

    public function handle()
    {
        $count = (int) $this->argument('count');

        $client = new Client([
            'url'       => env('INFLUXDB_URL'),
            'token'     => env('INFLUXDB_TOKEN'),
            'org'       => env('INFLUXDB_ORG'),
            'bucket'    => env('INFLUXDB_BUCKET'),
            'precision' => WritePrecision::S,
        ]);

        $writeApi = $client->createWriteApi();

        $macAddress = 'AA:BB:CC:DD:EE:02';

        for ($i = 0; $i < $count; $i++) {
            $timestamp = Carbon::now()->subSeconds($count - $i)->timestamp;

            // Energy measurement (todos os campos como float)
            $pointEnergy = Point::measurement('energy')
                ->addTag('mac', $macAddress)
                ->addTag('environment_id', (string)rand(1, 2))
                ->addField('consumption_kwh', (float) round(mt_rand(10, 500) / 100, 2))          // float
                ->addField('instantaneous_power', (float) mt_rand(100, 2000))           // convert para float (mesmo que inteiro, evita conflito)
                ->addField('voltage', (float) mt_rand(210, 240))                        // float
                ->addField('current', (float) mt_rand(1, 10))                          // float
                ->time($timestamp, WritePrecision::S);

            $writeApi->write($pointEnergy);

            // Temperature measurement
            $pointTemp = Point::measurement('temperature')
                ->addTag('mac', $macAddress)
                ->addTag('environment_id', (string)rand(1, 2))
                ->addField('temperature', round(mt_rand(180, 350) / 10, 1))  // float
                ->time($timestamp, WritePrecision::S);

            $writeApi->write($pointTemp);

            // Humidity measurement
            $pointHumidity = Point::measurement('humidity')
                ->addTag('mac', $macAddress)
                ->addTag('environment_id', (string)rand(1, 3))
                ->addField('humidity', round(mt_rand(300, 900) / 10, 1))    // float
                ->time($timestamp, WritePrecision::S);

            $writeApi->write($pointHumidity);
        }

        $client->close();

        $this->info("{$count} pontos falsos enviados para o Influx Cloud (bucket Iot).");
    }
}

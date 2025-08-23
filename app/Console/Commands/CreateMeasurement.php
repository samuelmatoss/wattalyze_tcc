<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use InfluxDB2\Client;
use InfluxDB2\Model\WritePrecision;
use InfluxDB2\Point;

class CreateMeasurement extends Command
{
    protected $signature = 'influx:create-measurement {name}';
    protected $description = 'Cria uma measurement no InfluxDB escrevendo um ponto básico';

    public function handle()
    {
        $measurementName = $this->argument('name');

        $client = new Client([
            'url' => config('influxdb.url'),
            'token' => config('influxdb.token'),
            'org' => config('influxdb.org'),
            'bucket' => config('influxdb.bucket'),
            'precision' => WritePrecision::S,
        ]);

        $writeApi = $client->createWriteApi();

        $point = Point::measurement($measurementName)
            ->addTag('init', 'true')
            ->addField('value', 0)
            ->time(time(), WritePrecision::S);

        $writeApi->write($point);

        $client->close();

        $this->info("Measurement '{$measurementName}' criada com ponto inicial.");
    }
}

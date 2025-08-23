<?php

namespace App\Providers;

use InfluxDB2\Client;
use Illuminate\Support\ServiceProvider;

class InfluxDBServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Client::class, function ($app) {
            return new Client([
                'url' => config('services.influxdb.url'),
                'token' => config('services.influxdb.token'),
                'bucket' => config('services.influxdb.bucket'),
                'org' => config('services.influxdb.org'),
            ]);
        });

        $this->app->bind('influxdb', function ($app) {
            return $app->make(Client::class);
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/influxdb.php' => config_path('influxdb.php'),
        ], 'config');

        // Configuração padrão
        config([
            'services.influxdb' => [
                'url' => env('INFLUXDB_URL', 'http://localhost:8086'),
                'token' => env('INFLUXDB_TOKEN', ''),
                'org' => env('INFLUXDB_ORG', 'wattalyze'),
                'bucket' => env('INFLUXDB_BUCKET', 'energy_data'),
                'retention' => env('INFLUXDB_RETENTION', '365d'),
            ]
        ]);
    }
}
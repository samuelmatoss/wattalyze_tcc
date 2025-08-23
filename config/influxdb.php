<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configurações do InfluxDB
    |--------------------------------------------------------------------------
    |
    | Defina aqui a URL, token, organização e bucket do InfluxDB, que serão
    | lidos a partir das variáveis de ambiente do arquivo .env.
    |
    */

    'url' => env('INFLUXDB_URL',''),

    'token' => env('INFLUXDB_TOKEN', ''),

    'org' => env('INFLUXDB_ORG', ''),

    'bucket' => env('INFLUXDB_BUCKET', ''),
];

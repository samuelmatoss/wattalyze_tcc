<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UserSeeder::class,
            DeviceTypeSeeder::class,
            EnergyTariffSeeder::class,
            EnvironmentSeeder::class,
            // Adicione outros seeders conforme necessário
        ]);
    }
}
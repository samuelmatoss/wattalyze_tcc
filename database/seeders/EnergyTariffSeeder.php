<?php

namespace Database\Seeders;

use App\Models\EnergyTariff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class EnergyTariffSeeder extends Seeder
{
    public function run()
    {
        $tariffs = [
            // Tarifa Convencional
            [
                'name' => 'Tarifa Convencional - CPFL Paulista',
                'provider' => 'CPFL Paulista',
                'region' => 'SP',
                'tariff_type' => 'conventional',
                'peak_rate' => 0.789,
                'off_peak_rate' => 0.789,
                'intermediate_rate' => null,
                'peak_hours_start' => null,
                'peak_hours_end' => null,
                'tax_rate' => 18.0,
                'valid_from' => Carbon::now()->subYear(),
                'valid_until' => Carbon::now()->addYear(),
                'is_active' => true,
            ],
            
            // Tarifa Branca
            [
                'name' => 'Tarifa Branca - CPFL Paulista',
                'provider' => 'CPFL Paulista',
                'region' => 'SP',
                'tariff_type' => 'white',
                'peak_rate' => 1.025,
                'off_peak_rate' => 0.645,
                'intermediate_rate' => 0.789,
                'peak_hours_start' => '17:00',
                'peak_hours_end' => '21:00',
                'tax_rate' => 18.0,
                'valid_from' => Carbon::now()->subYear(),
                'valid_until' => Carbon::now()->addYear(),
                'is_active' => true,
            ],
            
            // Tarifa Verde
            [
                'name' => 'Tarifa Verde - Light',
                'provider' => 'Light',
                'region' => 'RJ',
                'tariff_type' => 'green',
                'peak_rate' => 0.850,
                'off_peak_rate' => 0.450,
                'intermediate_rate' => null,
                'peak_hours_start' => '18:00',
                'peak_hours_end' => '22:00',
                'tax_rate' => 20.0,
                'valid_from' => Carbon::now()->subYear(),
                'valid_until' => Carbon::now()->addYear(),
                'is_active' => true,
            ],
            
            // Tarifa Azul
            [
                'name' => 'Tarifa Azul - Enel',
                'provider' => 'Enel',
                'region' => 'RJ',
                'tariff_type' => 'blue',
                'peak_rate' => 1.200,
                'off_peak_rate' => 0.600,
                'intermediate_rate' => 0.800,
                'peak_hours_start' => '18:00',
                'peak_hours_end' => '21:00',
                'tax_rate' => 20.0,
                'valid_from' => Carbon::now()->subYear(),
                'valid_until' => Carbon::now()->addYear(),
                'is_active' => true,
            ],
            
            // Bandeira Vermelha Patamar 1
            [
                'name' => 'Bandeira Vermelha Patamar 1',
                'provider' => 'Nacional',
                'region' => 'BR',
                'tariff_type' => 'flag',
                'peak_rate' => 0.04187,
                'off_peak_rate' => 0.04187,
                'intermediate_rate' => null,
                'peak_hours_start' => null,
                'peak_hours_end' => null,
                'tax_rate' => 0.0,
                'valid_from' => Carbon::now()->subMonths(3),
                'valid_until' => Carbon::now()->subMonths(2),
                'is_active' => false,
            ],
            
            // Bandeira Vermelha Patamar 2
            [
                'name' => 'Bandeira Vermelha Patamar 2',
                'provider' => 'Nacional',
                'region' => 'BR',
                'tariff_type' => 'flag',
                'peak_rate' => 0.06243,
                'off_peak_rate' => 0.06243,
                'intermediate_rate' => null,
                'peak_hours_start' => null,
                'peak_hours_end' => null,
                'tax_rate' => 0.0,
                'valid_from' => Carbon::now()->subMonth(),
                'valid_until' => Carbon::now()->addMonth(),
                'is_active' => true,
            ],
        ];

        foreach ($tariffs as $tariff) {
            EnergyTariff::create($tariff);
        }

        $this->command->info('Tarifas energéticas criadas com sucesso!');
    }
}
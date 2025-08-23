<?php

namespace Database\Seeders;

use App\Models\DeviceType;
use Illuminate\Database\Seeder;

class DeviceTypeSeeder extends Seeder
{
    public function run()
    {
        $deviceTypes = [
            [
                'name' => 'Medidor Inteligente',
                'category' => 'sensor',
                'typical_power_consumption' => 5.0,
                'icon' => 'fas fa-bolt',
                'description' => 'Medidor de energia inteligente para monitoramento em tempo real'
            ],
            [
                'name' => 'Ar Condicionado',
                'category' => 'hvac',
                'typical_power_consumption' => 1500.0,
                'icon' => 'fas fa-wind',
                'description' => 'Sistema de ar condicionado residencial'
            ],
            [
                'name' => 'Lâmpada LED Inteligente',
                'category' => 'lighting',
                'typical_power_consumption' => 10.0,
                'icon' => 'fas fa-lightbulb',
                'description' => 'Lâmpada LED controlável via Wi-Fi'
            ],
            [
                'name' => 'Geladeira',
                'category' => 'appliance',
                'typical_power_consumption' => 200.0,
                'icon' => 'fas fa-blender',
                'description' => 'Geladeira frost-free'
            ],
            [
                'name' => 'Câmera de Segurança',
                'category' => 'security',
                'typical_power_consumption' => 8.0,
                'icon' => 'fas fa-camera',
                'description' => 'Câmera IP com visão noturna'
            ],
            [
                'name' => 'Computador Desktop',
                'category' => 'other',
                'typical_power_consumption' => 250.0,
                'icon' => 'fas fa-desktop',
                'description' => 'Computador de mesa para escritório'
            ],
        ];

        foreach ($deviceTypes as $type) {
            DeviceType::create($type);
        }

        $this->command->info('Tipos de dispositivos criados com sucesso!');
    }
}
<?php

namespace Database\Seeders;

use App\Models\Environment;
use App\Models\User;
use Illuminate\Database\Seeder;

class EnvironmentSeeder extends Seeder
{
    public function run()
    {
        // Obter todos os usuários
        $users = User::all();
        
        foreach ($users as $user) {
            // Ambiente residencial padrão
            $home = Environment::create([
                'user_id' => $user->id,
                'name' => 'Minha Casa',
                'type' => 'residential',
                'size_sqm' => 120.0,
                'occupancy' => 4,
                'voltage_standard' => '220V',
                'tariff_type' => 'white',
                'energy_provider' => 'CPFL Paulista',
                'is_default' => true,
                'settings' => json_encode([
                    'peak_hours' => ['17:00-21:00'],
                    'notifications' => [
                        'high_consumption' => true,
                        'device_offline' => true
                    ]
                ])
            ]);
            
            // Cômodos residenciais
            $residentialRooms = [
                ['name' => 'Sala de Estar', 'type' => 'residential', 'size_sqm' => 25.0],
                ['name' => 'Cozinha', 'type' => 'residential', 'size_sqm' => 15.0],
                ['name' => 'Quarto Principal', 'type' => 'residential', 'size_sqm' => 20.0],
                ['name' => 'Quarto de Hóspedes', 'type' => 'residential', 'size_sqm' => 15.0],
                ['name' => 'Banheiro', 'type' => 'residential', 'size_sqm' => 8.0],
            ];
            
            foreach ($residentialRooms as $room) {
                Environment::create([
                    'user_id' => $user->id,
                    'parent_id' => $home->id,
                    'name' => $room['name'],
                    'type' => $room['type'],
                    'size_sqm' => $room['size_sqm'],
                ]);
            }
            
            // Ambiente comercial para 30% dos usuários
            if ($user->id % 3 === 0) {
                $office = Environment::create([
                    'user_id' => $user->id,
                    'name' => 'Meu Escritório',
                    'type' => 'commercial',
                    'size_sqm' => 80.0,
                    'occupancy' => 10,
                    'voltage_standard' => '220V',
                    'tariff_type' => 'blue',
                    'energy_provider' => 'Enel',
                    'settings' => json_encode([
                        'business_hours' => ['08:00-18:00'],
                        'notifications' => [
                            'after_hours_consumption' => true
                        ]
                    ])
                ]);
                
                // Áreas comerciais
                $commercialRooms = [
                    ['name' => 'Recepção', 'type' => 'commercial', 'size_sqm' => 15.0],
                    ['name' => 'Sala de Reuniões', 'type' => 'commercial', 'size_sqm' => 20.0],
                    ['name' => 'Área de Trabalho', 'type' => 'commercial', 'size_sqm' => 30.0],
                    ['name' => 'Copa', 'type' => 'commercial', 'size_sqm' => 15.0],
                ];
                
                foreach ($commercialRooms as $room) {
                    Environment::create([
                        'user_id' => $user->id,
                        'parent_id' => $office->id,
                        'name' => $room['name'],
                        'type' => $room['type'],
                        'size_sqm' => $room['size_sqm'],
                    ]);
                }
            }
        }
        
        $this->command->info('Ambientes padrão criados com sucesso!');
    }
}
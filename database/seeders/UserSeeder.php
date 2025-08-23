<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Criar usuário administrador
        User::create([
            'name' => 'Admin Wattalyze',
            'email' => 'admin@wattalyze.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'phone' => '+5511999999999',
            'avatar' => null,
            'timezone' => 'America/Sao_Paulo',
            'language' => 'pt-BR',
            'status' => 'active',
            'preferences' => json_encode([
                'dashboard' => [
                    'default_view' => 'weekly',
                    'energy_unit' => 'kWh',
                    'currency' => 'BRL'
                ],
                'notifications' => [
                    'email' => true,
                    'sms' => false,
                    'push' => true,
                    'alert_types' => ['critical', 'high']
                ]
            ])
        ]);

        // Criar usuário técnico
        User::create([
            'name' => 'Técnico Energia',
            'email' => 'tecnico@wattalyze.com',
            'password' => Hash::make('password123'),
            'role' => 'technician',
            'phone' => '+5511988888888',
            'status' => 'active'
        ]);

        // Criar 10 usuários demo
        User::factory()->count(10)->create();

        $this->command->info('Usuários iniciais criados com sucesso!');
    }
}
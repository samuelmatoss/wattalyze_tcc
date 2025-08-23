<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('password'), // password
            'remember_token' => Str::random(10),
            'role' => $this->faker->randomElement(['admin', 'user', 'technician']),
            'phone' => $this->faker->phoneNumber(),
            'avatar' => $this->faker->imageUrl(100, 100, 'people'),
            'timezone' => $this->faker->timezone(),
            'language' => $this->faker->randomElement(['pt-BR', 'en', 'es']),
            'status' => $this->faker->randomElement(['active', 'inactive', 'suspended']),
            'preferences' => json_encode([
                'notifications' => [
                    'email' => $this->faker->boolean(80),
                    'sms' => $this->faker->boolean(20),
                    'push' => $this->faker->boolean(90),
                    'alert_types' => ['critical', 'high']
                ],
                'dashboard' => [
                    'default_view' => $this->faker->randomElement(['daily', 'weekly', 'monthly']),
                    'show_costs' => true,
                    'show_carbon' => false
                ]
            ]),
        ];
    }

    public function admin()
    {
        return $this->state([
            'role' => 'admin',
        ]);
    }

    public function technician()
    {
        return $this->state([
            'role' => 'technician',
        ]);
    }

    public function withDevices($count = 3)
    {
        return $this->afterCreating(function (User $user) use ($count) {
            $user->devices()->saveMany(
                \App\Models\Device::factory()->count($count)->make()
            );
        });
    }
}
<?php

namespace Database\Factories;

use App\Models\Device;
use App\Models\DeviceType;
use App\Models\Environment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeviceFactory extends Factory
{
    protected $model = Device::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word . ' ' . $this->faker->randomElement(['Sensor', 'Meter', 'Monitor']),
            'mac_address' => $this->faker->macAddress,
            'serial_number' => $this->faker->unique()->uuid,
            'model' => $this->faker->bothify('Model-??##'),
            'manufacturer' => $this->faker->company,
            'firmware_version' => $this->faker->numerify('v#.#.#'),
            'status' => $this->faker->randomElement(['active', 'inactive', 'maintenance', 'error']),
            'location' => $this->faker->randomElement(['Living Room', 'Kitchen', 'Bedroom', 'Garage', 'Office']),
            'installation_date' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'rated_power' => $this->faker->randomFloat(2, 50, 2000),
            'rated_voltage' => $this->faker->randomElement([110, 220]),
            'device_type_id' => DeviceType::factory(),
            'user_id' => User::factory(),
            'environment_id' => Environment::factory(),
            'settings' => json_encode([
                'sampling_rate' => $this->faker->randomElement([1, 5, 15]),
                'reporting_interval' => $this->faker->randomElement([5, 15, 60]),
                'thresholds' => [
                    'power' => $this->faker->numberBetween(500, 1500),
                    'current' => $this->faker->randomFloat(2, 5, 20)
                ]
            ]),
        ];
    }

    public function active()
    {
        return $this->state([
            'status' => 'active',
            'last_seen_at' => now(),
        ]);
    }

    public function offline()
    {
        return $this->state([
            'status' => 'inactive',
            'last_seen_at' => now()->subHours(2),
        ]);
    }

    public function withEnergyData($count = 100)
    {
        return $this->afterCreating(function (Device $device) use ($count) {
            $device->energyConsumptions()->saveMany(
                \App\Models\EnergyConsumption::factory()
                    ->count($count)
                    ->for($device)
                    ->make()
            );
        });
    }
}
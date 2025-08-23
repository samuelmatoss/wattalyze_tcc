<?php

namespace Database\Factories;

use App\Models\Device;
use App\Models\EnergyConsumption;
use Illuminate\Database\Eloquent\Factories\Factory;

class EnergyConsumptionFactory extends Factory
{
    protected $model = EnergyConsumption::class;

    public function definition()
    {
        $timestamp = $this->faker->dateTimeBetween('-1 month', 'now');
        $hour = $timestamp->format('H');
        $isPeakHour = ($hour >= 17 && $hour < 21) || ($hour >= 8 && $hour < 12);
        
        return [
            'device_id' => Device::factory(),
            'timestamp' => $timestamp,
            'consumption_kwh' => $this->faker->randomFloat(6, 0.001, 10),
            'instantaneous_power' => $this->faker->randomFloat(2, 10, 2000),
            'voltage' => $this->faker->randomFloat(2, 110, 220),
            'current' => $this->faker->randomFloat(3, 0.1, 10),
            'power_factor' => $this->faker->randomFloat(3, 0.8, 1),
            'frequency' => $this->faker->randomFloat(1, 59.5, 60.5),
            'temperature' => $this->faker->randomFloat(1, 20, 40),
            'humidity' => $this->faker->randomFloat(1, 30, 80),
            'is_peak_hour' => $isPeakHour,
            'cost_estimate' => $this->faker->randomFloat(4, 0.0001, 0.5),
            'quality_score' => $this->faker->numberBetween(80, 100),
        ];
    }

    public function withDevice($deviceId)
    {
        return $this->state([
            'device_id' => $deviceId,
        ]);
    }

    public function peakHour()
    {
        return $this->state(function (array $attributes) {
            $hour = $this->faker->numberBetween(17, 20); // Horário de pico
            return [
                'timestamp' => $this->faker->dateTimeBetween('-1 month', 'now')->setTime($hour, 0),
                'is_peak_hour' => true,
                'instantaneous_power' => $this->faker->randomFloat(2, 800, 2000),
            ];
        });
    }

    public function offPeakHour()
    {
        return $this->state(function (array $attributes) {
            $hour = $this->faker->numberBetween(0, 7); // Madrugada
            return [
                'timestamp' => $this->faker->dateTimeBetween('-1 month', 'now')->setTime($hour, 0),
                'is_peak_hour' => false,
                'instantaneous_power' => $this->faker->randomFloat(2, 10, 300),
            ];
        });
    }

    public function withSpike()
    {
        return $this->state([
            'instantaneous_power' => $this->faker->randomFloat(2, 1500, 2500),
            'consumption_kwh' => $this->faker->randomFloat(6, 0.1, 0.5),
        ]);
    }
}
<?php

namespace Database\Factories;

use App\Models\Alert;
use App\Models\Device;
use App\Models\Environment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AlertFactory extends Factory
{
    protected $model = Alert::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'device_id' => Device::factory(),
            'environment_id' => Environment::factory(),
            'type' => $this->faker->randomElement(['consumption_spike', 'offline_device', 'high_cost', 'maintenance_due', 'anomaly']),
            'severity' => $this->faker->randomElement(['low', 'medium', 'high', 'critical']),
            'title' => $this->faker->sentence,
            'message' => $this->faker->paragraph,
            'threshold_value' => $this->faker->randomFloat(4, 10, 1000),
            'actual_value' => $this->faker->randomFloat(4, 10, 1000),
            'is_read' => $this->faker->boolean,
            'is_resolved' => $this->faker->boolean(70),
            'resolved_at' => $this->faker->optional(0.3)->dateTimeBetween('-1 week', 'now'),
            'metadata' => json_encode([
                'source' => $this->faker->randomElement(['rule', 'manual', 'system']),
                'triggered_at' => $this->faker->dateTimeThisMonth,
                'related_consumption' => $this->faker->randomNumber(5),
            ]),
        ];
    }

    public function critical()
    {
        return $this->state([
            'severity' => 'critical',
            'is_resolved' => false,
        ]);
    }

    public function unresolved()
    {
        return $this->state([
            'is_resolved' => false,
            'resolved_at' => null,
        ]);
    }

    public function consumptionSpike()
    {
        return $this->state([
            'type' => 'consumption_spike',
            'title' => 'Power Consumption Spike Detected',
            'message' => 'Device exceeded power threshold',
            'severity' => 'high',
            'threshold_value' => 1500,
            'actual_value' => $this->faker->numberBetween(1600, 2500),
        ]);
    }

    public function forDevice($deviceId)
    {
        return $this->state([
            'device_id' => $deviceId,
        ]);
    }
}
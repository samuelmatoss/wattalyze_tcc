<?php

namespace App\Jobs;

use App\Models\Device;
use App\Models\EnergyConsumption;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Events\NewEnergyData;
use App\Events\EnergyDataProcessed;

class ProcessEnergyDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $deviceId;

    public function __construct($deviceId, array $data)
    {
        $this->deviceId = $deviceId;
        $this->data = $data;
    }

    public function handle()
    {
        try {
            $device = Device::find($this->deviceId);
            
            if (!$device) {
                Log::error("Device not found: {$this->deviceId}");
                return;
            }

            // Validar e armazenar dados
            $consumption = EnergyConsumption::create([
                'device_id' => $this->deviceId,
                'timestamp' => now(),
                'consumption_kwh' => $this->data['consumption_kwh'],
                'instantaneous_power' => $this->data['instantaneous_power'] ?? null,
                'voltage' => $this->data['voltage'] ?? null,
                'current' => $this->data['current'] ?? null,
                'power_factor' => $this->data['power_factor'] ?? null,
                'frequency' => $this->data['frequency'] ?? null,
                'temperature' => $this->data['temperature'] ?? null,
                'humidity' => $this->data['humidity'] ?? null,
                'is_peak_hour' => $this->isPeakHour(),
            ]);

            // Atualizar último visto do dispositivo
            $device->update(['last_seen_at' => now()]);

            // Disparar eventos para processamento adicional
            event(new EnergyDataProcessed($consumption));

        } catch (\Exception $e) {
            Log::error("ProcessEnergyDataJob failed: " . $e->getMessage(), [
                'device_id' => $this->deviceId,
                'data' => $this->data
            ]);
        }
    }

    protected function isPeakHour()
    {
        $hour = now()->hour;
        return ($hour >= 17 && $hour < 21) || ($hour >= 8 && $hour < 12);
    }
}
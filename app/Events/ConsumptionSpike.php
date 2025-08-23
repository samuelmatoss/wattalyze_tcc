<?php

namespace App\Events;

use App\Models\Device;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConsumptionSpike implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $device;
    public $consumption;
    public $threshold;
    public $timestamp;
    public $spikeFactor;

    public function __construct(Device $device, $consumption, $threshold)
    {
        $this->device = $device;
        $this->consumption = $consumption;
        $this->threshold = $threshold;
        $this->timestamp = now();
        $this->spikeFactor = round(($consumption / $threshold) * 100, 2);
    }

    public function broadcastOn()
    {
        // Canal privado para o proprietário do dispositivo
        return new Channel('user.' . $this->device->user_id);
    }

    public function broadcastWith()
    {
        return [
            'device_id' => $this->device->id,
            'device_name' => $this->device->name,
            'consumption' => $this->consumption,
            'threshold' => $this->threshold,
            'spike_factor' => $this->spikeFactor,
            'timestamp' => $this->timestamp->toIso8601String(),
            'severity' => $this->spikeFactor > 300 ? 'critical' : ($this->spikeFactor > 200 ? 'high' : 'medium'),
            'message' => "Pico de consumo em {$this->device->name}: {$this->consumption}W (limite: {$this->threshold}W)"
        ];
    }
}
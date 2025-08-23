<?php

namespace App\Events;

use App\Models\Device;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeviceOffline implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $device;
    public $timestamp;
    public $lastSeen;
    public $durationMinutes;

    public function __construct(Device $device, $lastSeen, $durationMinutes)
    {
        $this->device = $device;
        $this->timestamp = now();
        $this->lastSeen = $lastSeen;
        $this->durationMinutes = $durationMinutes;
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
            'timestamp' => $this->timestamp->toIso8601String(),
            'last_seen' => $this->lastSeen->toIso8601String(),
            'duration_minutes' => $this->durationMinutes,
            'severity' => $this->durationMinutes > 60 ? 'critical' : ($this->durationMinutes > 30 ? 'high' : 'medium'),
            'message' => "Dispositivo {$this->device->name} offline há {$this->durationMinutes} minutos"
        ];
    }
}
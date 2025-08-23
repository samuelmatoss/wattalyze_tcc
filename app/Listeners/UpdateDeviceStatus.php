<?php

namespace App\Listeners;

use App\Events\DeviceHeartbeat;
use App\Events\DeviceStatusChanged;
use App\Models\Device;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateDeviceStatus implements ShouldQueue
{
    public function handle(DeviceHeartbeat $event)
    {
        $device = $event->device;
        $previousStatus = $device->status;
        $newStatus = 'online';
        
        // Atualizar último visto
        $device->last_seen_at = now();
        
        // Verificar se o status mudou
        if ($device->status !== 'online') {
            $device->status = 'online';
            $newStatus = 'online';
        }
        
        $device->save();
        
        // Se o status mudou, disparar evento
        if ($previousStatus !== $newStatus) {
            event(new DeviceStatusChanged(
                $device, 
                "Device came online after " . now()->diffInMinutes($device->last_seen_at) . " minutes",
                [
                    'previous_status' => $previousStatus,
                    'downtime_minutes' => now()->diffInMinutes($device->last_seen_at)
                ]
            ));
        }
    }
}
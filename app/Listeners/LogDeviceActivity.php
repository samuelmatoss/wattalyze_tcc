<?php

namespace App\Listeners;

use App\Events\DeviceStatusChanged;
use App\Models\DeviceActivityLog;
use App\Models\DeviceStat;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogDeviceActivity implements ShouldQueue
{
    public function handle(DeviceStatusChanged $event)
    {
        $device = $event->device;
        
        // Registrar atividade
        DeviceActivityLog::create([
            'device_id' => $device->id,
            'status' => $device->status,
            'message' => $event->message,
            'data' => $event->additionalData
        ]);
        
        // Atualizar estatísticas
        $stat = DeviceStat::firstOrCreate(['device_id' => $device->id]);
        
        if ($device->status === 'online') {
            $stat->increment('online_count');
            $stat->last_online = now();
        } else {
            $stat->increment('offline_count');
            $stat->last_offline = now();
        }
        
        $stat->save();
        
        // Calcular uptime (apenas se estiver online)
        if ($device->status === 'online') {
            $this->calculateUptime($device);
        }
    }
    
    protected function calculateUptime($device)
    {
        $lastOffline = DeviceActivityLog::where('device_id', $device->id)
            ->where('status', 'offline')
            ->latest()
            ->first();
            
        if ($lastOffline) {
            $uptime = now()->diffInMinutes($lastOffline->created_at);
            $device->stats()->update(['current_uptime' => $uptime]);
        }
    }
}
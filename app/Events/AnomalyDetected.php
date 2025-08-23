<?php

namespace App\Events;

use App\Models\Device;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AnomalyDetected implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $device;
    public $anomalyType;
    public $anomalousData;
    public $confidenceLevel;
    public $timestamp;

    public function __construct(Device $device, $anomalyType, $anomalousData, $confidenceLevel)
    {
        $this->device = $device;
        $this->anomalyType = $anomalyType;
        $this->anomalousData = $anomalousData;
        $this->confidenceLevel = $confidenceLevel;
        $this->timestamp = now();
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
            'anomaly_type' => $this->anomalyType,
            'anomalous_data' => $this->anomalousData,
            'confidence_level' => $this->confidenceLevel,
            'timestamp' => $this->timestamp->toIso8601String(),
            'severity' => $this->confidenceLevel > 90 ? 'critical' : ($this->confidenceLevel > 75 ? 'high' : 'medium'),
            'message' => "Anomalia detectada em {$this->device->name}: " . $this->getAnomalyDescription()
        ];
    }

    protected function getAnomalyDescription()
    {
        $descriptions = [
            'power_spike' => 'Pico de energia incomum',
            'power_drop' => 'Queda de energia prolongada',
            'constant_load' => 'Carga constante anormal',
            'voltage_fluctuation' => 'Flutuação de tensão',
            'unexpected_offline' => 'Desconexão inesperada',
            'behavior_change' => 'Mudança de padrão de consumo'
        ];
        
        return $descriptions[$this->anomalyType] ?? 'Comportamento anômalo detectado';
    }
}
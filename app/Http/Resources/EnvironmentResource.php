<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EnvironmentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'size_sqm' => (float) $this->size_sqm,
            'occupancy' => (int) $this->occupancy,
            'voltage_standard' => $this->voltage_standard,
            'energy_provider' => $this->energy_provider,
            'is_default' => (bool) $this->is_default,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
            
            // Relacionamentos
            'devices' => DeviceResource::collection($this->whenLoaded('devices')),
            
            // Dados calculados
            'total_devices' => $this->devices_count ?? $this->devices->count(),
            'total_consumption' => $this->getTotalConsumption(),
            'current_power' => $this->getCurrentPower(),
            'daily_average' => $this->getDailyAverage(),
            
            // Hierarquia
            'parent_environment' => $this->whenLoaded('parent', function () {
                return [
                    'id' => $this->parent->id,
                    'name' => $this->parent->name,
                ];
            }),
            
            'child_environments' => EnvironmentResource::collection($this->whenLoaded('children')),
            
            // Links
            'self' => route('api.environments.show', $this->id),
            'devices_link' => route('api.devices.index', ['environment_id' => $this->id]),
        ];
    }
    
    protected function getTotalConsumption()
    {
        if (!$this->relationLoaded('devices.energyConsumptions')) {
            return null;
        }
        
        return $this->devices->sum(function ($device) {
            return $device->energyConsumptions->sum('consumption_kwh');
        });
    }
    
    protected function getCurrentPower()
    {
        if (!$this->relationLoaded('devices.energyConsumptions')) {
            return null;
        }
        
        return $this->devices->sum(function ($device) {
            return $device->energyConsumptions->last()->instantaneous_power ?? 0;
        });
    }
    
    protected function getDailyAverage()
    {
        if (!$this->relationLoaded('devices.energyConsumptionAggregates')) {
            return null;
        }
        
        $total = 0;
        $count = 0;
        
        foreach ($this->devices as $device) {
            foreach ($device->energyConsumptionAggregates as $aggregate) {
                if ($aggregate->period_type === 'daily') {
                    $total += $aggregate->total_consumption_kwh;
                    $count++;
                }
            }
        }
        
        return $count > 0 ? $total / $count : 0;
    }
}
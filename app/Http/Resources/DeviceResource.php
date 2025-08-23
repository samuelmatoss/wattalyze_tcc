<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DeviceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'mac_address' => $this->mac_address,
            'serial_number' => $this->serial_number,
            'model' => $this->model,
            'manufacturer' => $this->manufacturer,
            'status' => $this->status,
            'location' => $this->location,
            'last_seen_at' => $this->last_seen_at?->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
            
            // Relacionamentos
            'device_type' => $this->whenLoaded('deviceType', function () {
                return [
                    'id' => $this->deviceType->id,
                    'name' => $this->deviceType->name,
                    'category' => $this->deviceType->category,
                    'icon' => $this->deviceType->icon,
                ];
            }),
            
            'environment' => $this->whenLoaded('environment', function () {
                return [
                    'id' => $this->environment->id,
                    'name' => $this->environment->name,
                ];
            }),
            
            // Dados calculados
            'current_power' => $this->when(
                $this->relationLoaded('energyConsumptions') && $this->energyConsumptions->isNotEmpty(),
                $this->energyConsumptions->last()->instantaneous_power
            ),
            
            'today_consumption' => $this->whenAggregated('energyConsumptions', 'consumption_kwh', 'sum', [
                'timestamp' => now()->startOfDay()
            ]),
            
            // Links
            'links' => [
                'self' => route('api.devices.show', $this->id),
                'energy_data' => route('api.energy-data.index', ['device_id' => $this->id]),
            ],
        ];
    }
}
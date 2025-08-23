<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EnergyConsumptionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'device_id' => $this->device_id,
            'timestamp' => $this->timestamp->toIso8601String(),
            'consumption_kwh' => (float) number_format($this->consumption_kwh, 6),
            'instantaneous_power' => (float) number_format($this->instantaneous_power ?? 0, 2),
            'voltage' => (float) number_format($this->voltage ?? 0, 2),
            'current' => (float) number_format($this->current ?? 0, 3),
            'power_factor' => (float) number_format($this->power_factor ?? 0, 3),
            'is_peak_hour' => (bool) $this->is_peak_hour,
            'cost_estimate' => (float) number_format($this->cost_estimate ?? 0, 4),
            'cost_estimate_currency' => 'BRL',
            
            // Dados calculados
            'carbon_footprint' => $this->calculateCarbonFootprint(),
            
            // Links
            'device' => $this->whenLoaded('device', function () {
                return [
                    'id' => $this->device->id,
                    'name' => $this->device->name,
                ];
            }),
        ];
    }
    
    protected function calculateCarbonFootprint()
    {
        // Fator de emissão médio do Brasil (kgCO2e/kWh)
        $emissionFactor = 0.1; // Valor hipotético para exemplo
        return $this->consumption_kwh * $emissionFactor;
    }
}

// Resource para dados agregados
class EnergyConsumptionAggregateResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'period_start' => $this->period_start->toIso8601String(),
            'period_end' => $this->period_end->toIso8601String(),
            'period_type' => $this->period_type,
            'total_consumption_kwh' => (float) number_format($this->total_consumption_kwh, 6),
            'avg_power' => (float) number_format($this->avg_power, 2),
            'max_power' => (float) number_format($this->max_power, 2),
            'min_power' => (float) number_format($this->min_power, 2),
            'total_cost' => (float) number_format($this->total_cost, 2),
            'peak_consumption_kwh' => (float) number_format($this->peak_consumption_kwh, 6),
            'off_peak_consumption_kwh' => (float) number_format($this->off_peak_consumption_kwh, 6),
            'carbon_footprint' => (float) number_format($this->total_consumption_kwh * 0.1, 2), // Exemplo
        ];
    }
}
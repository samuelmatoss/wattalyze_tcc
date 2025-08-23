<?php

namespace App\Services;

use App\Models\Device;
use Carbon\Carbon;

class CostService
{
    public function calculateDailyCost(Device $device)
    {
        // Exemplo de cálculo: supondo que device->current_power seja em Watts
        // e que o custo por kWh está disponível como $device->cost_per_kwh
        // Calcula o consumo diário aproximado
        $hours = Carbon::now()->hour + Carbon::now()->minute / 60;
        $dailyConsumptionKwh = ($device->current_power * $hours) / 1000;
        $costPerKwh = $device->cost_per_kwh ?? 1; // valor padrão se não definido
        return round($dailyConsumptionKwh * $costPerKwh, 2);
    }
}

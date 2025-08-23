<?php

namespace App\Services;

use App\Models\EnergyTariff;
use App\Models\Device;
use Carbon\Carbon;
use app\Models\EnergyConsumption;
class TariffService
{
    public function calculateEnergyCost(Device $device, float $consumption, Carbon $date): float
    {
        $tariff = $this->getActiveTariff($device->environment, $date);
        $isPeak = $this->isPeakHour($date, $tariff);
        
        $rate = $isPeak ? $tariff->peak_rate : $tariff->off_peak_rate;
        $cost = $consumption * $rate;
        
        // Aplicar impostos
        $cost *= (1 + ($tariff->tax_rate / 100));
        
        // Aplicar bandeira tarifária
        $cost += $this->applyTariffFlag($consumption, $date);
        
        return $cost;
    }

    public function getActiveTariff($environment, Carbon $date): EnergyTariff
    {
        return EnergyTariff::where('region', $environment->region)
            ->where('tariff_type', $environment->tariff_type)
            ->where('valid_from', '<=', $date)
            ->where('valid_until', '>=', $date)
            ->firstOrFail();
    }

    protected function isPeakHour(Carbon $date, EnergyTariff $tariff): bool
    {
        if (!$tariff->peak_hours_start) return false;
        
        $time = $date->format('H:i');
        return $time >= $tariff->peak_hours_start && $time < $tariff->peak_hours_end;
    }

    protected function applyTariffFlag(float $consumption, Carbon $date): float
    {
        $flag = EnergyTariff::where('tariff_type', 'flag')
            ->where('valid_from', '<=', $date)
            ->where('valid_until', '>=', $date)
            ->first();
        
        return $flag ? $consumption * $flag->peak_rate : 0;
    }

    public function compareTariffs(float $consumption, array $tariffs): array
    {
        $results = [];
        $now = now();
        
        foreach ($tariffs as $tariff) {
            $cost = $consumption * $tariff->peak_rate;
            $cost *= (1 + ($tariff->tax_rate / 100));
            
            $results[] = [
                'tariff_id' => $tariff->id,
                'name' => $tariff->name,
                'total_cost' => $cost,
                'potential_savings' => 0, // Calculado posteriormente
            ];
        }
        
        // Calcular economia potencial
        $minCost = min(array_column($results, 'total_cost'));
        foreach ($results as &$result) {
            $result['potential_savings'] = $result['total_cost'] - $minCost;
        }
        
        return $results;
    }

    public function forecastMonthlyCost(Device $device): array
    {
        $currentMonth = now()->startOfMonth();
        $dailyAvg = EnergyConsumption::where('device_id', $device->id)
            ->where('created_at', '>=', $currentMonth)
            ->avg('consumption_kwh');
            
        $daysInMonth = $currentMonth->daysInMonth;
        $estimatedConsumption = $dailyAvg * $daysInMonth;
        
        return [
            'estimated_consumption' => $estimatedConsumption,
            'estimated_cost' => $this->calculateEnergyCost($device, $estimatedConsumption, now()),
        ];
    }
}
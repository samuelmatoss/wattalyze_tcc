<?php

namespace App\Services;

use App\Models\User;
use App\Models\Device;
use App\Models\Alert;
use Carbon\Carbon;
use App\Models\EnergyConsumption;
use Illuminate\Support\Facades\DB;

class MetricsService
{
    public function getSystemMetrics(): array
    {
        return [
            'users' => [
                'total' => User::count(),
                'active' => User::where('last_login_at', '>', now()->subWeek())->count(),
            ],
            'devices' => [
                'total' => Device::count(),
                'online' => Device::where('status', 'active')->count(),
                'offline' => Device::where('status', 'inactive')->count(),
            ],
            'alerts' => [
                'last_24h' => Alert::where('created_at', '>', now()->subDay())->count(),
                'active' => Alert::where('is_resolved', false)->count(),
            ],
            'performance' => [
                'data_points' => $this->getDataPointsCount(),
                'processing_time' => $this->getAvgProcessingTime(),
            ]
        ];
    }

    public function getUserMetrics(User $user): array
    {
        return [
            'devices' => [
                'total' => $user->devices()->count(),
                'by_status' => $user->devices()->select('status', DB::raw('count(*) as count'))
                    ->groupBy('status')
                    ->get()
                    ->pluck('count', 'status'),
            ],
            'consumption' => [
                'today' => $this->getDailyConsumption($user),
                'monthly' => $this->getMonthlyConsumption($user),
                'comparison' => $this->compareToPreviousMonth($user),
            ],
            'costs' => [
                'current_month' => $this->getCurrentMonthCost($user),
                'estimated_next_month' => $this->estimateNextMonthCost($user),
            ]
        ];
    }
    
    protected function getDataPointsCount(): int
    {
        return EnergyConsumption::where('created_at', '>', now()->subDay())->count();
    }
    
    protected function getAvgProcessingTime(): float
    {
        // Simular tempo médio de processamento
        return rand(50, 150) / 1000; // segundos
    }
    
    protected function getDailyConsumption(User $user): float
    {
        return $user->devices()
            ->join('energy_consumptions', 'devices.id', '=', 'energy_consumptions.device_id')
            ->whereDate('energy_consumptions.timestamp', today())
            ->sum('consumption_kwh');
    }
    
    protected function getMonthlyConsumption(User $user): float
    {
        return $user->devices()
            ->join('energy_consumption_aggregates', 'devices.id', '=', 'energy_consumption_aggregates.device_id')
            ->where('period_type', 'daily')
            ->where('period_start', '>=', now()->startOfMonth())
            ->sum('total_consumption_kwh');
    }
    
    protected function compareToPreviousMonth(User $user): array
    {
        $current = $this->getMonthlyConsumption($user);
        $previous = $user->devices()
            ->join('energy_consumption_aggregates', 'devices.id', '=', 'energy_consumption_aggregates.device_id')
            ->where('period_type', 'daily')
            ->where('period_start', '>=', now()->subMonth()->startOfMonth())
            ->where('period_start', '<', now()->startOfMonth())
            ->sum('total_consumption_kwh');
            
        $change = $previous ? (($current - $previous) / $previous) * 100 : 0;
        
        return [
            'current' => $current,
            'previous' => $previous,
            'change_percent' => $change,
            'direction' => $change >= 0 ? 'up' : 'down',
        ];
    }
    
    protected function getCurrentMonthCost(User $user): float
    {
        return $user->devices()
            ->join('energy_consumption_aggregates', 'devices.id', '=', 'energy_consumption_aggregates.device_id')
            ->where('period_type', 'daily')
            ->where('period_start', '>=', now()->startOfMonth())
            ->sum('total_cost');
    }
    
    protected function estimateNextMonthCost(User $user): float
    {
        $current = $this->getCurrentMonthCost($user);
        $daysPassed = now()->day;
        $daysInMonth = now()->daysInMonth;
        
        return $current / $daysPassed * $daysInMonth;
    }
}
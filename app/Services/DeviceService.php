<?php

namespace App\Services;

use App\Models\Device;
use App\Services\EnergyAnalysisService;
use Carbon\Carbon;
use App\Models\DeviceStatusLog;
use App\Models\EnergyConsumption;
use Illuminate\Support\Facades\DB;

class DeviceService
{
    protected $energyAnalysis;

    public function __construct(EnergyAnalysisService $energyAnalysis)
    {
        $this->energyAnalysis = $energyAnalysis;
    }

    public function registerDevice(array $data): Device
    {
        $device = Device::create($data);
        
        // Configurações iniciais
        $this->applyDefaultSettings($device);
        
        return $device;
    }

    protected function applyDefaultSettings(Device $device)
    {
        $defaults = [
            'sampling_rate' => 15, // minutos
            'reporting_interval' => 60, // minutos
            'alert_thresholds' => [
                'power' => $device->rated_power * 0.9,
                'offline' => 30 // minutos
            ]
        ];
        
        $device->update(['settings' => $defaults]);
    }

    public function updateDeviceStatus(Device $device, string $status)
    {
        $previousStatus = $device->status;
        
        $device->update([
            'status' => $status,
            'last_status_change' => now(),
        ]);
        
        if ($previousStatus !== $status) {
            DeviceStatusLog::create([
                'device_id' => $device->id,
                'previous_status' => $previousStatus,
                'new_status' => $status,
                'duration' => $device->last_seen_at->diffInMinutes(now()),
            ]);
        }
    }

    public function runDiagnostics(Device $device): array
    {
        return [
            'connectivity' => $this->checkConnectivity($device),
            'power_quality' => $this->checkPowerQuality($device),
            'consumption_analysis' => $this->energyAnalysis->calculateEnergyMetrics(
                $device, 
                now()->subDay(), 
                now()
            ),
            'anomalies' => $this->energyAnalysis->detectAnomalies(
                $device,
                now()->subWeek(),
                now()
            )->count(),
        ];
    }

    protected function checkConnectivity(Device $device): array
    {
        $uptime = DeviceStatusLog::where('device_id', $device->id)
            ->where('new_status', 'active')
            ->sum('duration');
            
        $downtime = DeviceStatusLog::where('device_id', $device->id)
            ->where('new_status', 'inactive')
            ->sum('duration');
            
        return [
            'uptime_percentage' => $uptime / ($uptime + $downtime) * 100,
            'last_seen' => $device->last_seen_at->diffForHumans(),
            'response_time' => $this->pingDevice($device),
        ];
    }
    
    protected function pingDevice(Device $device): float
    {
        // Simulação de teste de ping
        return rand(10, 100) / 1000; // segundos
    }

    protected function checkPowerQuality(Device $device): array
    {
        $data = EnergyConsumption::where('device_id', $device->id)
            ->where('created_at', '>', now()->subDay())
            ->select(
                DB::raw('AVG(voltage) as avg_voltage'),
                DB::raw('STDDEV(voltage) as voltage_deviation'),
                DB::raw('AVG(power_factor) as avg_pf'),
                DB::raw('AVG(frequency) as avg_frequency')
            )
            ->first();
            
        return [
            'voltage_stability' => $data->voltage_deviation < 5 ? 'good' : 'unstable',
            'power_factor' => $data->avg_pf > 0.9 ? 'good' : 'low',
            'frequency_stability' => abs($data->avg_frequency - 60) < 0.5 ? 'good' : 'unstable',
        ];
    }
}
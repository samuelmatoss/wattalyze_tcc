<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Alert;
use App\Models\EnergyConsumptionAggregate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;  
class DashboardController extends Controller
{
    public function overview()
    {
        $user = auth()->user();
        
        // Dados de consumo do último mês
        $consumption = EnergyConsumptionAggregate::selectRaw('SUM(total_consumption_kwh) as total, period_start')
            ->whereHas('device', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('period_type', 'daily')
            ->where('period_start', '>=', now()->subDays(30))
            ->groupBy('period_start')
            ->get();
            
        // Alertas ativos
        $activeAlerts = Alert::where('user_id', $user->id)
            ->where('is_resolved', false)
            ->count();
            
        // Status dos dispositivos
        $devicesStatus = Device::where('user_id', $user->id)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();
            
        return response()->json([
            'consumption' => $consumption,
            'active_alerts' => $activeAlerts,
            'devices_status' => $devicesStatus,
            'total_devices' => Device::where('user_id', $user->id)->count()
        ]);
    }

    public function consumption(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'period' => 'required|in:day,week,month,year',
            'device_id' => 'nullable|exists:devices,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $periodType = match($request->period) {
            'day' => 'hourly',
            'week' => 'daily',
            'month', 'year' => 'daily'
        };

        $query = EnergyConsumptionAggregate::with('device')
            ->whereHas('device', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->where('period_type', $periodType);
            
        if ($request->device_id) {
            $query->where('device_id', $request->device_id);
        }
        
        // Filtro por período
        $end = now();
        $start = match($request->period) {
            'day' => now()->subDay(),
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            'year' => now()->subYear(),
        };
        
        $data = $query->whereBetween('period_start', [$start, $end])
            ->orderBy('period_start')
            ->get();
            
        return response()->json($data);
    }

    public function alerts()
    {
        $alerts = Alert::with('device')
            ->where('user_id', auth()->id())
            ->where('is_resolved', false)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        return response()->json($alerts);
    }

    public function devicesStatus()
    {
        $devices = Device::where('user_id', auth()->id())
            ->select('id', 'name', 'status', 'last_seen_at')
            ->orderBy('status')
            ->get();
            
        return response()->json($devices);
    }
}
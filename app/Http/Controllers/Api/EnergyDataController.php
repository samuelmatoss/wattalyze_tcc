<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EnergyConsumption;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Events\NewEnergyData;
use App\Models\EnergyConsumptionAggregate;

class EnergyDataController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|exists:devices,id',
            'consumption_kwh' => 'required|numeric',
            'timestamp' => 'required|date',
            // ... outros campos
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = EnergyConsumption::create($validator->validated());
        
        // Disparar verificação de alertas
        event(new NewEnergyData($data));
        
        return response()->json($data, 201);
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|exists:devices,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = EnergyConsumption::where('device_id', $request->device_id)
            ->whereBetween('timestamp', [$request->start_date, $request->end_date])
            ->get();
            
        return response()->json($data);
    }

    public function realTime(Request $request)
    {
        $data = EnergyConsumption::with('device')
            ->whereHas('device', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->orderBy('timestamp', 'desc')
            ->limit(50)
            ->get();
            
        return response()->json($data);
    }

    public function aggregates(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|exists:devices,id',
            'period' => 'required|in:hourly,daily,weekly,monthly',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $aggregates = EnergyConsumptionAggregate::where('device_id', $request->device_id)
            ->where('period_type', $request->period)
            ->orderBy('period_start', 'desc')
            ->get();
            
        return response()->json($aggregates);
    }
}
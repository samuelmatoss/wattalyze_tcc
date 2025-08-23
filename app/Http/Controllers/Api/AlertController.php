<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\AlertRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AlertController extends Controller
{
    public function index(Request $request)
    {
        $alerts = Alert::with('device', 'environment')
            ->where('user_id', auth()->id())
            ->when($request->status, function ($query, $status) {
                if ($status === 'active') {
                    return $query->where('is_resolved', false);
                } elseif ($status === 'resolved') {
                    return $query->where('is_resolved', true);
                }
            })
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json($alerts);
    }

    public function storeRule(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:consumption_threshold,cost_threshold,offline_duration,anomaly_detection',
            'threshold_value' => 'required|numeric',
            // ... outros campos
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $rule = AlertRule::create(array_merge(
            $validator->validated(),
            ['user_id' => auth()->id()]
        ));
        
        return response()->json($rule, 201);
    }

    public function updateRule(Request $request, $id)
    {
        $rule = AlertRule::where('user_id', auth()->id())
            ->findOrFail($id);
            
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'is_active' => 'sometimes|boolean',
            // ... outros campos
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $rule->update($validator->validated());
        return response()->json($rule);
    }

    public function destroyRule($id)
    {
        $rule = AlertRule::where('user_id', auth()->id())
            ->findOrFail($id);
            
        $rule->delete();
        return response()->json(null, 204);
    }
}
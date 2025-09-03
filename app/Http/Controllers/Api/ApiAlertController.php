<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\AlertRule;
use App\Models\Device;
use App\Models\Environment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApiAlertController extends Controller
{
    /**
     * Get all alert rules for authenticated user
     */
    public function rules(): JsonResponse
    {
        $rules = AlertRule::where('user_id', auth()->id())
            ->with(['device', 'environment'])
            ->get();

        return response()->json([
            'rules' => $rules,
            'devices' => Device::where('user_id', auth()->id())->get(),
            'environments' => Environment::where('user_id', auth()->id())->get()
        ]);
    }

    /**
     * Create new alert rule
     */
    public function storeRule(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:consumption_threshold,cost_threshold,offline_duration,anomaly_detection',
            'threshold_value' => 'nullable|numeric',
            'device_id' => 'nullable|exists:devices,id',
            'environment_id' => 'nullable|exists:environments,id',
            'condition' => 'nullable|string',
            'notification_channels' => 'nullable|array',
            'notification_channels.*' => 'in:email',
        ]);

        $rule = AlertRule::create([
            'user_id' => auth()->id(),
            'device_id' => $request->device_id,
            'environment_id' => $request->environment_id,
            'name' => $validated['name'],
            'type' => $validated['type'],
            'threshold_value' => $validated['threshold_value'] ?? null,
            'condition' => $request->filled('condition') ? json_encode(['expression' => $request->condition]) : null,
            'notification_channels' => $request->filled('notification_channels') ? json_encode($request->notification_channels) : null,
        ]);

        return response()->json([
            'message' => 'Regra de alerta criada com sucesso!',
            'rule' => $rule
        ], 201);
    }

    /**
     * Get active alerts
     */
    public function active(): JsonResponse
    {
        $alerts = Alert::where('user_id', auth()->id())
            ->where('is_resolved', false)
            ->with(['device', 'environment'])
            ->latest()
            ->paginate(10);

        return response()->json(['alerts' => $alerts]);
    }

    /**
     * Get alert history
     */
    public function history(): JsonResponse
    {
        $alerts = Alert::where('user_id', auth()->id())
            ->with(['device', 'environment'])
            ->latest()
            ->paginate(20);

        return response()->json(['alerts' => $alerts]);
    }

    /**
     * Mark alert as resolved
     */
    public function markResolved(Alert $alert): JsonResponse
    {
        if ($alert->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $alert->update([
            'is_resolved' => true,
            'resolved_at' => now()
        ]);

        return response()->json(['message' => 'Alerta marcado como resolvido!']);
    }

    /**
     * Get single rule for editing
     */
    public function editRule(AlertRule $rule): JsonResponse
    {
        if ($rule->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'rule' => $rule->load(['device', 'environment']),
            'devices' => Device::where('user_id', auth()->id())->get(),
            'environments' => Environment::where('user_id', auth()->id())->get()
        ]);
    }

    /**
     * Update alert rule
     */
    public function updateRule(Request $request, AlertRule $rule): JsonResponse
    {
        if ($rule->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:consumption_threshold,cost_threshold,offline_duration,anomaly_detection',
            'threshold_value' => 'nullable|numeric',
            'device_id' => 'nullable|exists:devices,id',
            'environment_id' => 'nullable|exists:environments,id',
            'condition' => 'nullable|string',
            'notification_channels' => 'nullable|array',
            'notification_channels.*' => 'in:email',
        ]);

        $rule->update([
            'device_id' => $validated['device_id'] ?? null,
            'environment_id' => $validated['environment_id'] ?? null,
            'name' => $validated['name'],
            'type' => $validated['type'],
            'threshold_value' => $validated['threshold_value'] ?? null,
            'condition' => $request->filled('condition') ? json_encode(['expression' => $request->condition]) : null,
            'notification_channels' => $request->filled('notification_channels') ? json_encode($request->notification_channels) : null,
        ]);

        return response()->json(['message' => 'Regra de alerta atualizada com sucesso!']);
    }

    /**
     * Delete alert rule
     */
    public function destroyRule(AlertRule $rule): JsonResponse
    {
        if ($rule->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $rule->delete();

        return response()->json(['message' => 'Regra de alerta excluída com sucesso!']);
    }

    /**
     * Toggle rule status
     */
    public function toggleRule(AlertRule $rule): JsonResponse
    {
        if ($rule->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $rule->is_active = !$rule->is_active;
        $rule->save();

        return response()->json([
            'message' => 'Status da regra alterado com sucesso!',
            'is_active' => $rule->is_active
        ]);
    }

    /**
     * Acknowledge alert
     */
    public function acknowledge(Alert $alert): JsonResponse
    {
        if ($alert->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $alert->update(['is_read' => true]);

        return response()->json(['message' => 'Alerta marcado como lido!']);
    }

    /**
     * Bulk resolve alerts
     */
    public function bulkResolve(Request $request): JsonResponse
    {
        $ids = $request->input('alert_ids', []);

        Alert::whereIn('id', $ids)
            ->where('user_id', auth()->id())
            ->update([
                'is_resolved' => true,
                'resolved_at' => now()
            ]);

        return response()->json(['message' => 'Alertas selecionados foram resolvidos!']);
    }
}
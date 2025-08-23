<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\AlertRule;
use App\Models\Device;
use App\Models\Environment;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function rules()
    {
        $rules = AlertRule::where('user_id', auth()->id())
            ->with(['device', 'environment'])
            ->get();

        $devices = Device::where('user_id', auth()->id())->get();
        $environments = Environment::where('user_id', auth()->id())->get();

        return view('alerts.rules', compact('rules', 'devices', 'environments'));
    }

    public function storeRule(Request $request)
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

        AlertRule::create([
            'user_id' => auth()->id(),
            'device_id' => $request->device_id,
            'environment_id' => $request->environment_id,
            'name' => $validated['name'],
            'type' => $validated['type'],
            'threshold_value' => $validated['threshold_value'] ?? null,
            'condition' => $request->filled('condition') ? json_encode(['expression' => $request->condition]) : null,
            'notification_channels' => $request->filled('notification_channels') ? json_encode($request->notification_channels) : null,
            'time_window' => null, 
        ]);

        return redirect()->route('alerts.rules')
            ->with('success', 'Regra de alerta criada com sucesso!');
    }

    public function active()
    {
        $alerts = Alert::where('user_id', auth()->id())
            ->where('is_resolved', false)
            ->with(['device', 'environment'])
            ->latest()
            ->paginate(10);

        return view('alerts.active', compact('alerts'));
    }

    public function history()
    {
        $alerts = Alert::where('user_id', auth()->id())
            ->with(['device', 'environment'])
            ->latest()
            ->paginate(20);

        return view('alerts.history', compact('alerts'));
    }

    public function markResolved(Alert $alert)
    {


        if ($alert->user_id !== auth()->id()) {
            abort(403);
        }

        $alert->update([
            'is_resolved' => true,
            'resolved_at' => now()
        ]);

        return back()->with('success', 'Alerta marcado como resolvido!');
    }
    public function editRule(AlertRule $rule)
    {
        if ($rule->user_id !== auth()->id()) {
            abort(403);
        }

        $devices = Device::where('user_id', auth()->id())->get();
        $environments = Environment::where('user_id', auth()->id())->get();

        return view('alerts.edit_rule', compact('rule', 'devices', 'environments'));
    }

    public function updateRule(Request $request, AlertRule $rule)
    {
        if ($rule->user_id !== auth()->id()) {
            abort(403);
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

        return redirect()->route('alerts.rules')->with('success', 'Regra de alerta atualizada com sucesso!');
    }

    public function destroyRule(AlertRule $rule)
    {
        if ($rule->user_id !== auth()->id()) {
            abort(403);
        }

        $rule->delete();

        return redirect()->route('alerts.rules')->with('success', 'Regra de alerta excluída com sucesso!');
    }

    public function toggleRule(AlertRule $rule)
    {
        if ($rule->user_id !== auth()->id()) {
            abort(403);
        }

        $rule->is_active = !$rule->is_active;
        $rule->save();

        return redirect()->route('alerts.rules')->with('success', 'Status da regra alterado com sucesso!');

    }

    public function acknowledge(Alert $alert)
    {
        if ($alert->user_id !== auth()->id()) {
            abort(403);
        }

        $alert->update([
            'is_read' => true
        ]);

        return back()->with('success', 'Alerta marcado como lido!');
    }

    public function bulkResolve(Request $request)
    {
        $ids = $request->input('alert_ids', []);

        Alert::whereIn('id', $ids)
            ->where('user_id', auth()->id())
            ->update([
                'is_resolved' => true,
                'resolved_at' => now()
            ]);

        return back()->with('success', 'Alertas selecionados foram resolvidos!');
    }
}

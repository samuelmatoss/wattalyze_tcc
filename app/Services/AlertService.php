<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\AlertRule;
use App\Models\Device;
use App\Services\NotificationService;
use Carbon\Carbon;

class AlertService
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function evaluateAlertRules(Device $device = null)
    {
        $rules = AlertRule::with('device')
            ->where('is_active', true)
            ->when($device, fn($q) => $q->where('device_id', $device->id))
            ->get();
        
        foreach ($rules as $rule) {
            $this->evaluateRule($rule);
        }
    }

    protected function evaluateRule(AlertRule $rule)
    {
        $value = $this->getCurrentValue($rule);
        
        if ($this->checkCondition($rule, $value)) {
            $this->triggerAlert($rule, $value);
        }
    }

    protected function getCurrentValue(AlertRule $rule)
    {
        switch ($rule->type) {
            case 'consumption_threshold':
                return $rule->device->current_power;
            case 'cost_threshold':
                // Melhor delegar o cálculo para um serviço dedicado, ex: CostService
                return app(\App\Services\CostService::class)->calculateDailyCost($rule->device);
            case 'offline_duration':
                return $rule->device->last_seen_at->diffInMinutes(now());
            default:
                return 0;
        }
    }

    protected function checkCondition(AlertRule $rule, $value): bool
    {
        $condition = $rule->condition;
        
        switch ($condition['operator']) {
            case '>':
                return $value > $rule->threshold_value;
            case '>=':
                return $value >= $rule->threshold_value;
            case '<':
                return $value < $rule->threshold_value;
            case '<=':
                return $value <= $rule->threshold_value;
            case '==':
                return $value == $rule->threshold_value;
            default:
                return false;
        }
    }

    protected function triggerAlert(AlertRule $rule, $actualValue)
    {
        // Prevenir alertas duplicados
        $recentAlert = Alert::where('alert_rule_id', $rule->id)
            ->where('created_at', '>', now()->subMinutes(5))
            ->exists();
        
        if ($recentAlert) return;

        $alert = Alert::create([
            'user_id' => $rule->user_id,
            'device_id' => $rule->device_id,
            'environment_id' => $rule->environment_id,
            'alert_rule_id' => $rule->id,
            'type' => $rule->type,
            'severity' => $rule->severity,
            'title' => $this->generateAlertTitle($rule),
            'message' => $this->generateAlertMessage($rule, $actualValue),
            'threshold_value' => $rule->threshold_value,
            'actual_value' => $actualValue,
        ]);

        $this->notificationService->sendAlertNotification($alert);
    }


    protected function generateAlertTitle(AlertRule $rule): string
    {
        return match ($rule->type) {
            'consumption_threshold' => 'Pico de Consumo Detectado',
            'cost_threshold' => 'Custo Elevado de Energia',
            'offline_duration' => 'Dispositivo Offline',
            default => 'Alerta Disparado'
        };
    }

    protected function generateAlertMessage(AlertRule $rule, $actualValue): string
    {
        $device = $rule->device->name ?? 'Dispositivo';
        
        return match ($rule->type) {
            'consumption_threshold' => "{$device} excedeu o limite de consumo: {$actualValue}W > {$rule->threshold_value}W",
            'cost_threshold' => "Custo diário de {$device}: R$ {$actualValue} > R$ {$rule->threshold_value}",
            'offline_duration' => "{$device} offline há {$actualValue} minutos",
            default => "Regra de alerta disparada: {$rule->name}"
        };
    }



    public function resolveAlert(Alert $alert)
    {
        $alert->update([
            'is_resolved' => true,
            'resolved_at' => now(),
        ]);
    }
}
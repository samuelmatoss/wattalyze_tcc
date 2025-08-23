<?php

namespace App\Http\Requests\Alert;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Device;
use App\Models\Environment;
use App\Models\AlertRule;

class UpdateAlertRuleRequest extends FormRequest
{
    public function authorize()
    {
        $rule = AlertRule::find($this->route('alert_rule'));
        return $rule && $rule->user_id === auth()->id();
    }

    public function rules()
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'type' => ['sometimes', 'required', 'in:consumption_threshold,cost_threshold,offline_duration,anomaly_detection'],
            'device_id' => ['nullable', 'exists:devices,id'],
            'environment_id' => ['nullable', 'exists:environments,id'],
            'condition' => ['sometimes', 'required', 'array'],
            'condition.operator' => ['sometimes', 'required', 'in:>,<,>=,<=,==,!=,between'],
            'condition.value' => ['sometimes', 'required', 'numeric'],
            'condition.second_value' => ['required_if:condition.operator,between', 'numeric'],
            'threshold_value' => ['sometimes', 'required', 'numeric'],
            'time_window' => ['nullable', 'integer', 'min:1'],
            'notification_channels' => ['sometimes', 'required', 'array', 'min:1'],
            'notification_channels.*' => ['in:email,sms,push'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
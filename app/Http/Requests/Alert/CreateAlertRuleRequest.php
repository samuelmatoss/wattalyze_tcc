<?php

namespace App\Http\Requests\Alert;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Device;
use App\Models\Environment;

class CreateAlertRuleRequest extends FormRequest
{
    public function authorize()
    {
        // Verifica se o dispositivo/ambiente pertence ao usuário
        if ($this->device_id) {
            $device = Device::find($this->device_id);
            return $device && $device->user_id === auth()->id();
        }

        if ($this->environment_id) {
            $environment = Environment::find($this->environment_id);
            return $environment && $environment->user_id === auth()->id();
        }

        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:consumption_threshold,cost_threshold,offline_duration,anomaly_detection'],
            'device_id' => ['nullable', 'exists:devices,id'],
            'environment_id' => ['nullable', 'exists:environments,id'],
            'condition' => ['required', 'array'],
            'condition.operator' => ['required', 'in:>,<,>=,<=,==,!=,between'],
            'condition.value' => ['required', 'numeric'],
            'condition.second_value' => ['required_if:condition.operator,between', 'numeric'],
            'threshold_value' => ['required', 'numeric'],
            'time_window' => ['nullable', 'integer', 'min:1'], // em minutos
            'notification_channels' => ['required', 'array', 'min:1'],
            'notification_channels.*' => ['in:email,sms,push'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);

        return array_merge($data, [
            'user_id' => auth()->id(),
        ]);
    }
}

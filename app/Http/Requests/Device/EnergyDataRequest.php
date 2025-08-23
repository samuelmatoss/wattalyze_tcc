<?php

namespace App\Http\Requests\Device;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Device;

class EnergyDataRequest extends FormRequest
{
    public function rules()
    {
        return [
            'device_id' => ['required', 'exists:devices,id'],
            'timestamp' => ['required', 'date'],
            'consumption_kwh' => ['required', 'numeric', 'min:0'],
            'instantaneous_power' => ['nullable', 'numeric', 'min:0'],
            'voltage' => ['nullable', 'numeric', 'min:0'],
            'current' => ['nullable', 'numeric', 'min:0'],
            'power_factor' => ['nullable', 'numeric', 'between:-1,1'],
            'frequency' => ['nullable', 'numeric', 'min:0'],
            'temperature' => ['nullable', 'numeric'],
            'humidity' => ['nullable', 'numeric', 'between:0,100'],
            'is_peak_hour' => ['nullable', 'boolean'],
            'cost_estimate' => ['nullable', 'numeric', 'min:0'],
            'quality_score' => ['nullable', 'integer', 'between:0,100'],
        ];
    }

    public function authorize()
    {
        // Verifica se o dispositivo pertence ao usuário
        $device = Device::find($this->device_id);
        return $device && $device->user_id === auth()->id();
    }
}
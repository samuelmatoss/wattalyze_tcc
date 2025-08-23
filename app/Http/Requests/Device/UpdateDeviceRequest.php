<?php

namespace App\Http\Requests\Device;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\DeviceType;
use App\Models\Environment;
use App\Models\Device;
use Illuminate\Validation\Rule;

class UpdateDeviceRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('update', Device::find($this->route('device')));
    }

    public function rules()
    {
        $deviceId = $this->route('device');
        
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'mac_address' => [
                'sometimes', 
                'required', 
                'string', 
                'max:17',
                Rule::unique('devices')->ignore($deviceId)
            ],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'manufacturer' => ['nullable', 'string', 'max:255'],
            'firmware_version' => ['nullable', 'string', 'max:50'],
            'status' => ['sometimes', 'required', 'in:active,inactive,maintenance,error'],
            'location' => ['sometimes', 'required', 'string', 'max:255'],
            'installation_date' => ['nullable', 'date'],
            'rated_power' => ['nullable', 'numeric', 'min:0'],
            'rated_voltage' => ['nullable', 'numeric', 'min:0'],
            'device_type_id' => ['nullable', 'exists:device_types,id'],
            'environment_id' => ['nullable', 'exists:environments,id'],
            'settings' => ['nullable', 'json'],
        ];
    }
}
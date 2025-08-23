<?php

namespace App\Http\Requests\Device;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\DeviceType;
use App\Models\Environment;

class CreateDeviceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'mac_address' => ['required', 'string', 'max:17', 'unique:devices'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'manufacturer' => ['nullable', 'string', 'max:255'],
            'firmware_version' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:active,inactive,maintenance,error'],
            'location' => ['required', 'string', 'max:255'],
            'installation_date' => ['nullable', 'date'],
            'rated_power' => ['nullable', 'numeric', 'min:0'],
            'rated_voltage' => ['nullable', 'numeric', 'min:0'],
            'device_type_id' => ['nullable', 'exists:' . DeviceType::class . ',id'],
            'environment_id' => ['nullable', 'exists:' . Environment::class . ',id'],
            'settings' => ['nullable', 'json'],
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

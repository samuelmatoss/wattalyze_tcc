<?php

namespace App\Http\Requests\Environment;

use Illuminate\Foundation\Http\FormRequest;

class CreateEnvironmentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:residential,commercial,industrial,public'],
            'size_sqm' => ['nullable', 'numeric', 'min:0'],
            'occupancy' => ['nullable', 'integer', 'min:0'],
            'voltage_standard' => ['nullable', 'in:110V,220V,380V'],
            'tariff_type' => ['nullable', 'in:conventional,white,green,blue'],
            'energy_provider' => ['nullable', 'string', 'max:255'],
            'installation_date' => ['nullable', 'date'],
            'is_default' => ['nullable', 'boolean'],
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

<?php

namespace App\Http\Requests\Environment;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Environment;

class UpdateEnvironmentRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('update', Environment::find($this->route('environment')));
    }

    public function rules()
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['sometimes', 'required', 'in:residential,commercial,industrial,public'],
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
}
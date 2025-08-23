<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnergyConsumption extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'timestamp',
        'consumption_kwh',
        'instantaneous_power',
        'voltage',
        'current',
        'power_factor',
        'frequency',
        'temperature',
        'humidity',
        'is_peak_hour',
        'cost_estimate',
        'quality_score'
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'consumption_kwh' => 'float',
        'instantaneous_power' => 'float',
        'voltage' => 'float',
        'current' => 'float',
        'power_factor' => 'float',
        'frequency' => 'float',
        'temperature' => 'float',
        'humidity' => 'float',
        'is_peak_hour' => 'boolean',
        'cost_estimate' => 'float'
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
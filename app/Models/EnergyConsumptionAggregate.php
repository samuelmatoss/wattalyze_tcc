<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnergyConsumptionAggregate extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'period_type',
        'period_start',
        'period_end',
        'total_consumption_kwh',
        'avg_power',
        'max_power',
        'min_power',
        'total_cost',
        'peak_consumption_kwh',
        'off_peak_consumption_kwh',
        'data_points_count'
    ];

    protected $casts = [
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'total_consumption_kwh' => 'float',
        'avg_power' => 'float',
        'max_power' => 'float',
        'min_power' => 'float',
        'total_cost' => 'float',
        'peak_consumption_kwh' => 'float',
        'off_peak_consumption_kwh' => 'float'
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
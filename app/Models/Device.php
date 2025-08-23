<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'mac_address',
        'serial_number',
        'model',
        'manufacturer',
        'firmware_version',
        'status',
        'location',
        'installation_date',
        'rated_power',
        'rated_voltage',
        'device_type_id',
        'user_id',
        'environment_id',
              'last_seen_at'
    ];

    protected $casts = [
        'installation_date' => 'date',
        'rated_power' => 'float',
        'rated_voltage' => 'float',
        'last_seen_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function environment()
    {
        return $this->belongsTo(Environment::class);
    }

    public function deviceType()
    {
        return $this->belongsTo(DeviceType::class);
    }

    public function energyConsumptions()
    {
        return $this->hasMany(EnergyConsumption::class);
    }

    public function aggregates()
    {
        return $this->hasMany(EnergyConsumptionAggregate::class);
    }

    public function stats()
    {
        return $this->hasOne(DeviceStat::class);
    }

    public function activities()
    {
        return $this->hasMany(DeviceActivityLog::class);
    }
         public function latestConsumption()
    {
        
        return $this->hasOne(EnergyConsumption::class)->latestOfMany('timestamp');
       
    }
}
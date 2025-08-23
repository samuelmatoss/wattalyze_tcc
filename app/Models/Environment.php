<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Environment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'type',
        'size_sqm',
        'occupancy',
        'voltage_standard',
        'tariff_type',
        'energy_provider',
        'installation_date',
        'is_default',
        'settings',
        'user_id'
    ];

    protected $casts = [
        'size_sqm' => 'float',
        'installation_date' => 'date',
        'is_default' => 'boolean',
        'settings' => 'array',
        'deleted_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function parent()
    {
        return $this->belongsTo(Environment::class, 'parent_id');
    }
}
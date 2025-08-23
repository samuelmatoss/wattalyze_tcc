<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'typical_power_consumption',
        'icon',
        'description'
    ];

    protected $casts = [
        'typical_power_consumption' => 'float'
    ];

    public function devices()
    {
        return $this->hasMany(Device::class);
    }
}
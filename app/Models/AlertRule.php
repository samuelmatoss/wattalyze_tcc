<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_id',
        'environment_id',
        'name',
        'type',
        'condition',
        'threshold_value',
        'time_window',
        'notification_channels',
        'is_active'
    ];

    protected $casts = [
        'condition' => 'array',
        'threshold_value' => 'float',
        'notification_channels' => 'array',
        'is_active' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function environment()
    {
        return $this->belongsTo(Environment::class);
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }
}
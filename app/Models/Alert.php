<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_id',
        'environment_id',
        'alert_rule_id',
        'type',
        'severity',
        'title',
        'message',
        'threshold_value',
        'actual_value',
        'is_resolved',
        'is_read',
    ];

    protected $casts = [
        'threshold_value' => 'float',
        'actual_value' => 'float',
        'is_read' => 'boolean',
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
        'metadata' => 'array'
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

    public function alertRule()
    {
        return $this->belongsTo(AlertRule::class);
    }

    public function notifications()
    {
        return $this->hasMany(NotificationLog::class);
    }
}

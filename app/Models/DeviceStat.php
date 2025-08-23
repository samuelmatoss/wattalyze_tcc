<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceStat extends Model
{
    protected $fillable = [
        'device_id', 'online_count', 'offline_count', 'last_online', 'last_offline', 'current_uptime'
    ];
}

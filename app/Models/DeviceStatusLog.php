<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceStatusLog extends Model
{
    protected $fillable = [
        'device_id', 'previous_status', 'new_status', 'duration'
    ];
}

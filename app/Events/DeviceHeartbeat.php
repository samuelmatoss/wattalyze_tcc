<?php

namespace App\Events;

use App\Models\Device;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeviceHeartbeat
{
    use Dispatchable, SerializesModels;

    public $device;

    public function __construct(Device $device)
    {
        $this->device = $device;
    }
}

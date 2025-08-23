<?php

namespace App\Events;

use App\Models\Device;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeviceStatusChanged
{
    use Dispatchable, SerializesModels;

    public $device;
    public $message;
    public $additionalData;

    public function __construct(Device $device, $message, $additionalData = [])
    {
        $this->device = $device;
        $this->message = $message;
        $this->additionalData = $additionalData;
    }
}

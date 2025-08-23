<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class DeviceNotFoundException extends Exception
{
    protected $deviceId;

    public function __construct($deviceId, $message = "Device not found", $code = 404)
    {
        $this->deviceId = $deviceId;
        parent::__construct($message, $code);
    }

    public function report()
    {
        Log::error("Device not found: {$this->deviceId}", [
            'exception' => $this,
            'user_id' => auth()->id(),
        ]);
    }

    public function render($request)
    {
        return new JsonResponse([
            'error' => $this->getMessage(),
            'suggestions' => [
                'Verify the device ID',
                'Check your permissions for this device',
                'Ensure the device is properly registered'
            ],
            'device_id' => $this->deviceId,
            'documentation_url' => route('api.docs.devices')
        ], 404);
    }
}
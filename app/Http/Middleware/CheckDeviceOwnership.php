<?php

namespace App\Http\Middleware;

use App\Models\Device;
use Closure;
use Illuminate\Http\Request;

class CheckDeviceOwnership
{
    public function handle(Request $request, Closure $next)
    {
        $deviceId = $request->route('device') ?? $request->input('device_id');
        
        if (!$deviceId) {
            abort(400, 'Device ID not provided');
        }

        $device = Device::findOrFail($deviceId);

        if ($device->user_id !== auth()->id()) {
            abort(403, 'You do not have permission to access this device');
        }

        $request->merge(['device' => $device]);

        return $next($request);
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
class DeviceController extends Controller
{
    public function index()
    {
        $devices = Device::with('deviceType', 'environment')
            ->where('user_id', auth()->id())
            ->get();

        return response()->json($devices);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'mac_address' => [
                'required',
                'string',
                'max:17',
                'unique:devices',
                'regex:/^([0-9A-Fa-f]{2}:){5}[0-9A-Fa-f]{2}$/',
            ],
            'serial_number' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'firmware_version' => 'nullable|string|max:50',
            'status' => 'nullable|in:online,offline',
            'location' => 'nullable|string|max:255',
            'installation_date' => 'nullable|date',
            'rated_power' => 'nullable|numeric|min:0',
            'rated_voltage' => 'nullable|numeric|min:0',
            'device_type_id' => 'nullable|exists:device_types,id',
            'environment_id' => 'nullable|exists:environments,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $device = Device::create(array_merge(
            $validator->validated(),
            ['user_id' => auth()->id()]
        ));

        return response()->json($device, 201);
    }
   
    public function show($id)
    {
        $device = Device::with('energyConsumptions', 'alerts')
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return response()->json($device);
    }

    public function update(Request $request, $id)
    {
        $device = Device::where('user_id', auth()->id())
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'mac_address' => [
                'required',
                'string',
                'max:17',
                'unique:devices',
                'regex:/^([0-9A-Fa-f]{2}:){5}[0-9A-Fa-f]{2}$/',
            ],
            'serial_number' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'firmware_version' => 'nullable|string|max:50',
            'status' => 'nullable|in:online,offline',
            'location' => 'nullable|string|max:255',
            'installation_date' => 'nullable|date',
            'rated_power' => 'nullable|numeric|min:0',
            'rated_voltage' => 'nullable|numeric|min:0',
            'device_type_id' => 'nullable|exists:device_types,id',
            'environment_id' => 'nullable|exists:environments,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $device->update($validator->validated());
        return response()->json($device);
    }

    public function destroy($id)
    {
        $device = Device::where('user_id', auth()->id())
            ->findOrFail($id);

        $device->delete();
        return response()->json(null, 204);
    }
}

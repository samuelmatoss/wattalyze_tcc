<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EnergyTariff;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Alert;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    /**
     * Get user profile
     */
    public function profile(): JsonResponse
    {
        $user = auth()->user();
        
        return response()->json([
            'user' => $user->only(['id', 'name', 'email', 'created_at'])
        ]);
    }

    /**
     * Get user notifications
     */
    public function notifications(): JsonResponse
    {
        $user = auth()->user();
        $alerts = Alert::where('user_id', auth()->id())
            ->where('is_resolved', false)
            ->with(['device', 'environment'])
            ->latest()
            ->paginate(10);

        return response()->json([
            'user' => $user->only(['id', 'name', 'email']),
            'alerts' => $alerts
        ]);
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|current_password',
            'password' => 'required|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth()->user();
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'message' => 'Senha atualizada com sucesso!'
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update($validator->validated());

        return response()->json([
            'message' => 'Perfil atualizado com sucesso!',
            'user' => $user->only(['id', 'name', 'email'])
        ]);
    }

    /**
     * Get user notification preferences
     */
    public function notificationPreferences(): JsonResponse
    {
        $user = auth()->user();
        
        // Assuming you have notification preferences stored in the users table or a separate table
        $preferences = $user->notification_preferences ?? [
            'email_alerts' => true,
            'push_notifications' => false,
            'alert_thresholds' => [
                'energy' => 100,
                'temperature' => 30,
                'humidity' => 80
            ]
        ];

        return response()->json([
            'notification_preferences' => $preferences
        ]);
    }

    /**
     * Update user notification preferences
     */
    public function updateNotificationPreferences(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email_alerts' => 'boolean',
            'push_notifications' => 'boolean',
            'alert_thresholds.energy' => 'numeric|min:0',
            'alert_thresholds.temperature' => 'numeric|min:-50|max:100',
            'alert_thresholds.humidity' => 'numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth()->user();
        
        // Update notification preferences
        $user->notification_preferences = array_merge(
            (array) $user->notification_preferences,
            $validator->validated()
        );
        
        $user->save();

        return response()->json([
            'message' => 'Preferências de notificação atualizadas com sucesso!',
            'notification_preferences' => $user->notification_preferences
        ]);
    }
}


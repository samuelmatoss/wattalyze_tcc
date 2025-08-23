<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Broadcast::routes(['middleware' => ['auth:sanctum']]);

        // Canal para eventos específicos do usuário
        Broadcast::channel('user.{userId}', function ($user, $userId) {
            return (int) $user->id === (int) $userId;
        });

        // Canal para dispositivos
        Broadcast::channel('device.{deviceId}', function ($user, $deviceId) {
            return $user->devices()->where('id', $deviceId)->exists();
        });

        // Canal para ambientes
        Broadcast::channel('environment.{environmentId}', function ($user, $environmentId) {
            return $user->environments()->where('id', $environmentId)->exists();
        });

        // Configurar driver Pusher (exemplo)
        $pusherConfig = [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => true,
                'encrypted' => true,
            ],
        ];

        config(['broadcasting.connections.pusher' => $pusherConfig]);
    }
}
<?php

namespace App\Listeners;

use App\Events\DeviceOffline;
use App\Jobs\SendNotificationJob;

class SendOfflineNotification
{
    public function handle(DeviceOffline $event)
    {
        $channels = $event->device->user->preferences['notification_channels'] ?? ['email'];
        
        // Enfileirar job de notificação
        SendNotificationJob::dispatch([
            'type' => 'device_offline',
            'user_id' => $event->device->user_id,
            'title' => 'Dispositivo Offline',
            'message' => $event->broadcastWith()['message'],
            'metadata' => $event->broadcastWith()
        ], $channels);
    }
}
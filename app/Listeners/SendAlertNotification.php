<?php

namespace App\Listeners;

use App\Events\AlertTriggered;
use App\Jobs\SendNotificationJob;
use App\Models\Alert;
use App\Models\NotificationLog;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendAlertNotification implements ShouldQueue
{
    public function handle(AlertTriggered $event)
    {
        $alert = $event->alert;
        $user = $alert->user;
        
        try {
            // Obter canais de notificação preferidos do usuário
            $channels = $user->preferences['notification_channels'] ?? ['email'];
            
            // Enfileirar job para enviar notificação
            SendNotificationJob::dispatch($alert, $channels);
            
            // Registrar envio no log
            NotificationLog::create([
                'user_id' => $user->id,
                'alert_id' => $alert->id,
                'channels' => $channels,
                'status' => 'pending'
            ]);
            
        } catch (\Exception $e) {
            // Registrar falha
            NotificationLog::create([
                'user_id' => $user->id,
                'alert_id' => $alert->id,
                'channels' => $channels ?? [],
                'status' => 'failed',
                'error' => $e->getMessage()
            ]);
            
            // Reenfileirar para nova tentativa
            throw $e;
        }
    }
}
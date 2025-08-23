<?php

namespace App\Listeners;

use App\Events\AnomalyDetected;

class SendAnomalyNotification
{
    public function handle(AnomalyDetected $event)
    {
        // Lógica para enviar notificação de anomalia
    }
}

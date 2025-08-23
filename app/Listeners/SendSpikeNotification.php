<?php

namespace App\Listeners;

use App\Events\ConsumptionSpike;

class SendSpikeNotification
{
    public function handle(ConsumptionSpike $event)
    {
        // Lógica para enviar notificação de pico
    }
}

<?php

namespace App\Listeners;

use App\Events\HighCostAlert;

class SendCostNotification
{
    public function handle(HighCostAlert $event)
    {
        // Lógica para enviar notificação de custo elevado
    }
}

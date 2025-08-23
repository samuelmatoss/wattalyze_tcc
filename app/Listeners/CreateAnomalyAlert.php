<?php

namespace App\Listeners;

use App\Events\AnomalyDetected;

class CreateAnomalyAlert
{
    public function handle(AnomalyDetected $event)
    {
        // Lógica para criar alerta de anomalia
    }
}

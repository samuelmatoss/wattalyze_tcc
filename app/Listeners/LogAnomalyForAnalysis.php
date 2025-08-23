<?php

namespace App\Listeners;

use App\Events\AnomalyDetected;

class LogAnomalyForAnalysis
{
    public function handle(AnomalyDetected $event)
    {
        // Lógica para registrar anomalia para análise futura
    }
}

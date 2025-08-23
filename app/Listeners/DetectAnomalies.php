<?php

namespace App\Listeners;

use App\Events\EnergyDataProcessed;

class DetectAnomalies
{
    public function handle(EnergyDataProcessed $event)
    {
        // Lógica para detectar anomalias
    }
}

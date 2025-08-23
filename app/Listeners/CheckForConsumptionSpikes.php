<?php

namespace App\Listeners;

use App\Events\EnergyDataProcessed;

class CheckForConsumptionSpikes
{
    public function handle(EnergyDataProcessed $event)
    {
        // Lógica para detectar picos de consumo
    }
}

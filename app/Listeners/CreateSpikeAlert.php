<?php

namespace App\Listeners;

use App\Events\ConsumptionSpike;

class CreateSpikeAlert
{
    public function handle(ConsumptionSpike $event)
    {
        // Lógica para criar alerta de pico
    }
}

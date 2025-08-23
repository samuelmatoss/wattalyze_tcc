<?php

namespace App\Listeners;

use App\Events\HighCostAlert;

class CreateCostAlert
{
    public function handle(HighCostAlert $event)
    {
        // Lógica para criar alerta de custo elevado
    }
}

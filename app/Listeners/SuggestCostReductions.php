<?php

namespace App\Listeners;

use App\Events\HighCostAlert;

class SuggestCostReductions
{
    public function handle(HighCostAlert $event)
    {
        // Lógica para sugerir redução de custos
    }
}

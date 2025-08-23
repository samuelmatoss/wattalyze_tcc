<?php

namespace App\Events;

use App\Models\EnergyConsumption;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EnergyDataProcessed
{
    use Dispatchable, SerializesModels;

    public $consumption;

    public function __construct(EnergyConsumption $consumption)
    {
        $this->consumption = $consumption;
    }
}

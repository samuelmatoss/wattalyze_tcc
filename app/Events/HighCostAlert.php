<?php

namespace App\Events;

use App\Models\Environment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class HighCostAlert implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $environment;
    public $cost;
    public $averageCost;
    public $periodStart;
    public $periodEnd;
    public $devices;

    public function __construct(Environment $environment, $cost, $averageCost, $periodStart, $periodEnd, $devices = [])
    {
        $this->environment = $environment;
        $this->cost = $cost;
        $this->averageCost = $averageCost;
        $this->periodStart = $periodStart;
        $this->periodEnd = $periodEnd;
        $this->devices = $devices;
    }

    public function broadcastOn()
    {
        // Canal privado para o proprietário do ambiente
        return new Channel('user.' . $this->environment->user_id);
    }

    public function broadcastWith()
    {
        $costIncrease = round((($this->cost - $this->averageCost) / $this->averageCost * 100), 2);
        
        return [
            'environment_id' => $this->environment->id,
            'environment_name' => $this->environment->name,
            'cost' => $this->cost,
            'average_cost' => $this->averageCost,
            'cost_increase' => $costIncrease,
            'period_start' => $this->periodStart->toIso8601String(),
            'period_end' => $this->periodEnd->toIso8601String(),
            'devices' => $this->devices,
            'timestamp' => now()->toIso8601String(),
            'severity' => $costIncrease > 100 ? 'critical' : ($costIncrease > 50 ? 'high' : 'medium'),
            'message' => "Custo elevado em {$this->environment->name}: R$ {$this->cost} (média: R$ {$this->averageCost})"
        ];
    }
}
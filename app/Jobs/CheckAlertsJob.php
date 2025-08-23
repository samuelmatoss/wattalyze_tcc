<?php

namespace App\Jobs;

use App\Models\AlertRule;
use App\Models\Device;
use App\Models\Alert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Services\InfluxDBService;
use Carbon\Carbon;
use App\Mail\AlertNotificationMail;
use Illuminate\Support\Facades\Mail;

class CheckAlertsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $influx;

    public function handle()
    {
        $this->influx = app(InfluxDBService::class);

        $rules = AlertRule::with('device')
            ->where('is_active', true)
            ->whereIn('type', ['consumption_spike', 'consumption_threshold'])
            ->get();

        foreach ($rules as $rule) {
            try {
                $this->evaluateRule($rule);
            } catch (\Exception $e) {
                Log::error("Erro na regra {$rule->id}: " . $e->getMessage());
            }
        }
    }

    protected function evaluateRule(AlertRule $rule)
    {
        Log::info("Avaliando regra {$rule->id} do tipo {$rule->type}");

        switch ($rule->type) {
            case 'consumption_spike':
                $this->checkConsumptionSpike($rule);
                break;
            case 'consumption_threshold':
                $this->checkConsumptionThreshold($rule);
                break;
            default:
                Log::warning("Tipo de regra não suportado: {$rule->type}");
        }
    }

    protected function checkConsumptionSpike(AlertRule $rule)
    {
        Log::info("checkConsumptionSpike para regra {$rule->id}");

        $avgConsumption = $this->getAverageConsumption($rule, $rule->time_window ?? 10);
        $currentConsumption = $this->getLatestConsumption($rule);

        Log::info("Consumo médio: {$avgConsumption}, Consumo atual: {$currentConsumption}");

        if ($avgConsumption > 0 && $currentConsumption > ($avgConsumption * $rule->threshold_value)) {
            Log::info("Criando alerta para regra {$rule->id} devido a pico de consumo");
            $this->createAlert($rule, $currentConsumption);
        } else {
            Log::info("Condição de pico de consumo não satisfeita para regra {$rule->id}");
        }
    }

    protected function checkConsumptionThreshold(AlertRule $rule)
    {
        Log::info("checkConsumptionThreshold para regra {$rule->id}");

        $currentConsumption = $this->getLatestConsumption($rule);

        if ($currentConsumption > $rule->threshold_value) {
            Log::info("Criando alerta para regra {$rule->id} devido a limite de consumo excedido");
            $this->createAlert($rule, $currentConsumption);
        } else {
            Log::info("Consumo dentro do limite para regra {$rule->id}");
        }
    }

    protected function getAverageConsumption(AlertRule $rule, int $minutes)
    {
        if (!$rule->device) {
            Log::warning("Regra {$rule->id} não tem dispositivo associado");
            return 0;
        }

        $mac = $rule->device->mac_address;
        $bucket = $this->influx->getBucket();
        $start = Carbon::now()->subMinutes($minutes)->toIso8601String();

        $query = <<<FLUX
        from(bucket: "{$bucket}")
        |> range(start: {$start})
        |> filter(fn: (r) => r._measurement == "energy  ")
        |> filter(fn: (r) => r.mac == "{$mac}")
        |> filter(fn: (r) => r._field == "consumption_kwh")
        |> mean()
        FLUX;

        try {
            $result = $this->influx->queryEnergyData($query);
            return $result[0]['value'] ?? 0;
        } catch (\Exception $e) {
            Log::error("Erro ao calcular consumo médio: " . $e->getMessage());
            return 0;
        }
    }

    protected function getLatestConsumption(AlertRule $rule)
    {
        if (!$rule->device) {
            Log::warning("Regra {$rule->id} não tem dispositivo associado");
            return 0;
        }

        $mac = $rule->device->mac_address;
        $bucket = $this->influx->getBucket();

        $query = <<<FLUX
        from(bucket: "{$bucket}")
        |> range(start: -5m)
        |> filter(fn: (r) => r._measurement == "energy")
        |> filter(fn: (r) => r.mac == "{$mac}")
        |> filter(fn: (r) => r._field == "consumption_kwh")
        |> last()
        FLUX;

        try {
            $result = $this->influx->queryEnergyData($query);
            return $result[0]['value'] ?? 0;
        } catch (\Exception $e) {
            Log::error("Erro ao obter consumo atual: " . $e->getMessage());
            return 0;
        }
    }

   protected function createAlert(AlertRule $rule, $actualValue)
{
    $exists = Alert::where('alert_rule_id', $rule->id)
        ->where('created_at', '>', now()->subMinutes(5))
        ->exists();

    if ($exists) {
        Log::info("Alerta recente já existe para a regra {$rule->id}");
        return;
    }

    $alert = Alert::create([
        'user_id' => $rule->user_id,
        'device_id' => $rule->device_id,
        'environment_id' => $rule->environment_id,
        'alert_rule_id' => $rule->id,
        'type' => $rule->type,
        'severity' => $rule->severity,
        'title' => $this->getAlertTitle($rule),
        'message' => $this->getAlertMessage($rule, $actualValue),
        'threshold_value' => $rule->threshold_value,
        'actual_value' => $actualValue,
        'is_resolved' => false,
        'is_read' => false,
    ]);

    Log::info("Alerta criado para regra {$rule->id}");

    // === Notificação por email ===
    if ($rule->user && $rule->user->email) {
        Mail::to($rule->user->email)->queue(new AlertNotificationMail($alert));
        Log::info("Email enviado para {$rule->user->email}");
    }
}

    protected function getAlertTitle(AlertRule $rule)
    {
        return match ($rule->type) {
            'consumption_spike' => 'Pico de consumo detectado',
            'consumption_threshold' => 'Limite de consumo excedido',
            default => 'Alerta de consumo'
        };
    }

    protected function getAlertMessage(AlertRule $rule, $actualValue)
    {
        $device = $rule->device ? $rule->device->name : 'Dispositivo';
        $threshold = $rule->threshold_value;

        return match ($rule->type) {
            'consumption_spike' => "Pico de consumo em {$device}: {$actualValue} kWh (limite: {$threshold}x média)",
            'consumption_threshold' => "{$device} excedeu limite: {$actualValue} kWh > {$threshold} kWh",
            default => "Alerta de consumo em {$device}: {$actualValue} kWh"
        };
    }

}

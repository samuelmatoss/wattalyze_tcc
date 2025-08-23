<?php

namespace App\Jobs;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\Middleware\ThrottlesExceptions;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Device;
use App\Services\InfluxDBService;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class GenerateReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $report;

    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    /**
     * Calcula o custo baseado em faixas progressivas
     */
    private function calculateCost(float $consumption, $tariff): float
    {
        if (empty($tariff->bracket1_rate)) {
            return round($consumption * ($tariff->default_rate ?? 0), 4);
        }

        $cost = 0;
        $remaining = $consumption;

        // Definir faixas de consumo
        $brackets = [
            [
                'limit' => $tariff->bracket1_max ?? INF,
                'rate' => $tariff->bracket1_rate
            ],
            [
                'limit' => $tariff->bracket2_max ?? INF,
                'rate' => $tariff->bracket2_rate
            ],
            [
                'limit' => $tariff->bracket3_max ?? INF,
                'rate' => $tariff->bracket3_rate
            ],
        ];

        foreach ($brackets as $index => $bracket) {
            if ($remaining <= 0) break;

            // Calcular o limite da faixa atual
            $prevLimit = $index > 0 ? $brackets[$index - 1]['limit'] : 0;
            $currentLimit = min($bracket['limit'], $prevLimit + ($bracket['limit'] ?? INF)) - $prevLimit;

            // Calcular consumo na faixa atual
            $bracketConsumption = min($remaining, $currentLimit);
            $cost += $bracketConsumption * $bracket['rate'];
            $remaining -= $bracketConsumption;
        }

        // Aplicar taxa adicional se existir
        if (!empty($tariff->tax_rate)) {
            $cost += $cost * ($tariff->tax_rate / 100);
        }

        return round($cost, 4);
    }

    public function handle()
    {
        $report = $this->report;
        $user = $report->user;
        $tariff = $user->activeEnergyTariff;

        if (!$tariff) {
            throw new \Exception("Usuário não possui tarifa ativa configurada.");
        }

        $devices = Device::whereIn('id', $report->filters['devices'] ?? [])
            ->where('user_id', $user->id)
            ->get();

        if ($devices->isEmpty()) {
            throw new \Exception("Nenhum dispositivo válido selecionado para o relatório.");
        }

        $influx = app(InfluxDBService::class);
        $timezone = config('app.timezone', 'America/Sao_Paulo');
        $start = Carbon::parse($report->period_start)->startOfDay()->setTimezone('UTC')->toIso8601String();
        $end = Carbon::parse($report->period_end)->endOfDay()->setTimezone('UTC')->toIso8601String();

        $dailyData = [];
        $dailyCosts = [];
        $charts = [];

        foreach ($devices as $device) {
            $query = <<<FLUX
from(bucket: "{$influx->getBucket()}")
  |> range(start: $start, stop: $end)
  |> filter(fn: (r) => r._measurement == "energy")
  |> filter(fn: (r) => r.mac == "{$device->mac_address}")
  |> filter(fn: (r) => r._field == "consumption_kwh")
FLUX;

            try {
                $result = $influx->queryEnergyData($query);

                // Inicializa arrays para o dispositivo
                $dailyData[$device->name] = [];
                $dailyCosts[$device->name] = [];

                foreach ($result as $row) {
                    $localDate = Carbon::parse($row['time'])->setTimezone($timezone)->format('Y-m-d');
                    $consumption = round($row['value'] ?? 0, 4);

                    // Soma o consumo diário
                    if (!isset($dailyData[$device->name][$localDate])) {
                        $dailyData[$device->name][$localDate] = 0;
                    }
                    $dailyData[$device->name][$localDate] += $consumption;
                }

                // Calcula custo com base no consumo diário somado
                foreach ($dailyData[$device->name] as $date => $consumptionSum) {
                    $dailyCosts[$device->name][$date] = $this->calculateCost($consumptionSum, $tariff);
                }

                // Preencher dias faltantes no período com zero
                $periodStart = Carbon::parse($report->period_start);
                $periodEnd = Carbon::parse($report->period_end);
                $datesInPeriod = [];

                for ($date = $periodStart->copy(); $date->lte($periodEnd); $date->addDay()) {
                    $dateStr = $date->format('Y-m-d');
                    $datesInPeriod[] = $dateStr;

                    if (!isset($dailyData[$device->name][$dateStr])) {
                        $dailyData[$device->name][$dateStr] = 0;
                        $dailyCosts[$device->name][$dateStr] = 0;
                    }
                }

                // Ordenar por data
                ksort($dailyData[$device->name]);
                ksort($dailyCosts[$device->name]);

                // Gerar gráficos
                $labels = $datesInPeriod;
                $consumptionValues = array_values($dailyData[$device->name]);
                $costValues = array_values($dailyCosts[$device->name]);

                $charts[$device->name] = [
                    'consumption_chart_base64' => $this->generateChartBase64($labels, $consumptionValues, 'Energia Gasta (kWh)', '#27ae60'),
                    'cost_chart_base64' => $this->generateChartBase64($labels, $costValues, 'Custo (R$)', '#e74c3c'),
                ];
            } catch (\Exception $e) {
                Log::error("Erro ao processar dispositivo {$device->id}: " . $e->getMessage());
                continue;
            }
        }

        $reportData = [
            'report_name' => $report->name,
            'period_start' => Carbon::parse($report->period_start)->format('d/m/Y'),
            'period_end' => Carbon::parse($report->period_end)->format('d/m/Y'),
            'devices' => $dailyData,
            'costs' => $dailyCosts,
            'charts' => $charts
        ];

        $html = view('reports.templates.default', [
            'reportData' => $reportData,
        ])->render();

        $pdf = Pdf::loadHTML($html);
        $fileName = "reports/{$report->id}_" . now()->format('YmdHis') . ".pdf";
        Storage::put($fileName, $pdf->output());

        $report->update([
            'data' => $reportData,
            'status' => 'completed',
            'file_path' => $fileName,
        ]);
    }

    public function middleware(): array
    {
        return [
            new WithoutOverlapping($this->report->id),
            (new ThrottlesExceptions(5, 1))->backoff(30),
        ];
    }
    private function generateChartUrl(array $labels, array $data, string $label, string $color = 'blue'): string
    {
        $chartConfig = [
            'type' => 'line',
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => $label,
                    'backgroundColor' => $color,
                    'borderColor' => $color,
                    'fill' => false,
                    'data' => $data,
                ]]
            ],
            'options' => [
                'scales' => [
                    'yAxes' => [[
                        'ticks' => [
                            'beginAtZero' => true,
                        ],
                    ]],
                ],
            ],
        ];

        $encodedConfig = urlencode(json_encode($chartConfig));
        return "https://quickchart.io/chart?c={$encodedConfig}";
    }
    private function generateChartBase64(array $labels, array $data, string $label, string $color = 'blue'): string
    {
        $chartConfig = [
            'type' => 'line',
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => $label,
                    'backgroundColor' => $color,
                    'borderColor' => $color,
                    'fill' => false,
                    'data' => $data,
                ]],
            ],
            'options' => [
                'scales' => [
                    'yAxes' => [[
                        'ticks' => [
                            'beginAtZero' => true,
                        ],
                    ]],
                ],
            ],
        ];

        $encodedConfig = urlencode(json_encode($chartConfig));
        $url = "https://quickchart.io/chart?c={$encodedConfig}&format=png&backgroundColor=white&width=600&height=300";

        // Pega o conteúdo da imagem via HTTP
        $imageContents = file_get_contents($url);

        // Transforma para base64
        $base64 = base64_encode($imageContents);

        // Retorna no formato aceito pelo <img>
        return "data:image/png;base64,{$base64}";
    }
}

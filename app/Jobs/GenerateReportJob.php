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

            $prevLimit = $index > 0 ? $brackets[$index - 1]['limit'] : 0;
            $currentLimit = min($bracket['limit'], $prevLimit + ($bracket['limit'] ?? INF)) - $prevLimit;

            $bracketConsumption = min($remaining, $currentLimit);
            $cost += $bracketConsumption * $bracket['rate'];
            $remaining -= $bracketConsumption;
        }

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
        $summaryData = [];

        // Gerar período completo de datas
        $periodStart = Carbon::parse($report->period_start);
        $periodEnd = Carbon::parse($report->period_end);
        $datesInPeriod = [];

        for ($date = $periodStart->copy(); $date->lte($periodEnd); $date->addDay()) {
            $datesInPeriod[] = $date->format('Y-m-d');
        }

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
                $dailyData[$device->name] = array_fill_keys($datesInPeriod, 0);
                $dailyCosts[$device->name] = array_fill_keys($datesInPeriod, 0);

                foreach ($result as $row) {
                    $localDate = Carbon::parse($row['time'])->setTimezone($timezone)->format('Y-m-d');
                    $consumption = round($row['value'] ?? 0, 4);

                    if (isset($dailyData[$device->name][$localDate])) {
                        $dailyData[$device->name][$localDate] += $consumption;
                    }
                }

                // Calcula custos e dados de resumo
                $totalConsumption = 0;
                $totalCost = 0;
                $maxConsumption = 0;
                $minConsumption = PHP_FLOAT_MAX;

                foreach ($dailyData[$device->name] as $date => $consumptionSum) {
                    $cost = $this->calculateCost($consumptionSum, $tariff);
                    $dailyCosts[$device->name][$date] = $cost;
                    
                    $totalConsumption += $consumptionSum;
                    $totalCost += $cost;
                    
                    if ($consumptionSum > 0) {
                        $maxConsumption = max($maxConsumption, $consumptionSum);
                        $minConsumption = min($minConsumption, $consumptionSum);
                    }
                }

                $summaryData[$device->name] = [
                    'total_consumption' => round($totalConsumption, 2),
                    'total_cost' => round($totalCost, 2),
                    'avg_consumption' => round($totalConsumption / count($datesInPeriod), 2),
                    'avg_cost' => round($totalCost / count($datesInPeriod), 2),
                    'max_consumption' => round($maxConsumption, 2),
                    'min_consumption' => $minConsumption === PHP_FLOAT_MAX ? 0 : round($minConsumption, 2),
                ];

                // Gerar gráficos melhorados
                $labels = array_map(function($date) {
                    return Carbon::parse($date)->format('d/m');
                }, $datesInPeriod);

                $consumptionValues = array_values($dailyData[$device->name]);
                $costValues = array_values($dailyCosts[$device->name]);

                $charts[$device->name] = [
                    'consumption_chart_base64' => $this->generateAdvancedChartBase64(
                        $labels, 
                        $consumptionValues, 
                        'Consumo de Energia (kWh)', 
                        '#2E8B57',
                        'line'
                    ),
                    'cost_chart_base64' => $this->generateAdvancedChartBase64(
                        $labels, 
                        $costValues, 
                        'Custo Energético (R$)', 
                        '#DC143C',
                        'bar'
                    ),
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
            'charts' => $charts,
            'summary' => $summaryData,
            'total_devices' => count($devices),
            'generated_at' => now()->format('d/m/Y H:i')
        ];

        $html = view('reports.templates.default', [
            'reportData' => $reportData,
        ])->render();

        $pdf = Pdf::loadHTML($html)->setPaper('a4', 'portrait');
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

    /**
     * Gera gráfico avançado com melhor design
     */
    private function generateAdvancedChartBase64(array $labels, array $data, string $label, string $color = '#2E8B57', string $type = 'line'): string
    {
        // Cores gradiente para melhor visual
        $gradientColors = [
            '#2E8B57' => ['#2E8B57', '#32CD32'],  // Verde
            '#DC143C' => ['#DC143C', '#FF6347'],  // Vermelho
        ];

        $gradient = $gradientColors[$color] ?? [$color, $color];

        $chartConfig = [
            'type' => $type,
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => $label,
                    'data' => $data,
                    'backgroundColor' => $type === 'bar' ? $gradient[0] . '80' : $gradient[0] . '20',
                    'borderColor' => $gradient[0],
                    'borderWidth' => 3,
                    'fill' => $type === 'line' ? 'start' : false,
                    'tension' => 0.4,
                    'pointBackgroundColor' => $gradient[0],
                    'pointBorderColor' => '#ffffff',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 5,
                    'pointHoverRadius' => 7,
                ]]
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'layout' => [
                    'padding' => [
                        'top' => 20,
                        'bottom' => 20,
                        'left' => 20,
                        'right' => 20
                    ]
                ],
                'scales' => [
                    'yAxes' => [[
                        'ticks' => [
                            'beginAtZero' => true,
                            'fontColor' => '#666666',
                            'fontSize' => 12,
                            'fontStyle' => 'bold',
                            'callback' => '%%CALLBACK%%'
                        ],
                        'gridLines' => [
                            'color' => '#E5E5E5',
                            'lineWidth' => 1,
                            'drawBorder' => false
                        ]
                    ]],
                    'xAxes' => [[
                        'ticks' => [
                            'fontColor' => '#666666',
                            'fontSize' => 11,
                            'fontStyle' => 'bold'
                        ],
                        'gridLines' => [
                            'display' => false
                        ]
                    ]]
                ],
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                    'labels' => [
                        'fontColor' => '#333333',
                        'fontSize' => 14,
                        'fontStyle' => 'bold',
                        'usePointStyle' => true,
                        'padding' => 20
                    ]
                ],
                'plugins' => [
                    'datalabels' => [
                        'display' => false
                    ]
                ]
            ]
        ];

        // Adicionar formatação personalizada para os valores do eixo Y
        if (strpos($label, 'R$') !== false) {
            $chartConfig['options']['scales']['yAxes'][0]['ticks']['callback'] = 'function(value) { return "R$ " + value.toFixed(2); }';
        } else {
            $chartConfig['options']['scales']['yAxes'][0]['ticks']['callback'] = 'function(value) { return value.toFixed(2) + " kWh"; }';
        }

        $encodedConfig = urlencode(json_encode($chartConfig));
        $url = "https://quickchart.io/chart?c={$encodedConfig}&format=png&backgroundColor=white&width=800&height=400&devicePixelRatio=2";

        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 30,
                    'method' => 'GET'
                ]
            ]);
            
            $imageContents = file_get_contents($url, false, $context);
            
            if ($imageContents === false) {
                throw new \Exception('Falha ao gerar gráfico');
            }

            $base64 = base64_encode($imageContents);
            return "data:image/png;base64,{$base64}";
            
        } catch (\Exception $e) {
            Log::error("Erro ao gerar gráfico: " . $e->getMessage());
            return $this->generateFallbackChart($labels, $data, $label, $color);
        }
    }

    /**
     * Gera um gráfico simples como fallback
     */
    private function generateFallbackChart(array $labels, array $data, string $label, string $color): string
    {
        $chartConfig = [
            'type' => 'line',
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => $label,
                    'data' => $data,
                    'borderColor' => $color,
                    'backgroundColor' => $color . '40',
                    'fill' => true
                ]]
            ],
            'options' => [
                'scales' => [
                    'yAxes' => [['ticks' => ['beginAtZero' => true]]]
                ]
            ]
        ];

        $encodedConfig = urlencode(json_encode($chartConfig));
        $url = "https://quickchart.io/chart?c={$encodedConfig}&format=png&backgroundColor=white&width=600&height=300";
        
        $imageContents = file_get_contents($url);
        $base64 = base64_encode($imageContents);
        return "data:image/png;base64,{$base64}";
    }
}
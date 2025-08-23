<?php

namespace App\Services;

use App\Models\Report;
use App\Models\Device;
use App\Models\EnergyConsumptionAggregate;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EnergyReportExport;
use Illuminate\Support\Facades\Storage;

class ReportService
{
    public function generateReport(Report $report): string
    {
        $data = $this->collectReportData($report);
        $filePath = "reports/{$report->id}_" . now()->format('YmdHis') . ".{$report->format}";

        switch ($report->format) {
            case 'pdf':
                $pdf = Pdf::loadView('reports.energy', $data);
                Storage::put($filePath, $pdf->output());
                break;

            case 'excel':
            case 'csv':
                $export = new EnergyReportExport($data['data']);
                Excel::store($export, $filePath);
                break;

            case 'json':
            default:
                Storage::put($filePath, json_encode($data));
        }

        return $filePath;
    }
    protected function collectReportData(Report $report): array
    {
        $query = EnergyConsumptionAggregate::where('period_type', $report->period_type)
            ->whereBetween('period_start', [$report->period_start, $report->period_end]);

        if ($report->filters['devices'] ?? false) {
            $query->whereIn('device_id', $report->filters['devices']);
        }

        $data = $query->get();

        $summary = [
            'total_consumption' => $data->sum('total_consumption_kwh'),
            'total_cost' => $data->sum('total_cost'),
            'average_power' => $data->avg('avg_power'),
            'peak_consumption' => $data->sum('peak_consumption_kwh'),
            'off_peak_consumption' => $data->sum('off_peak_consumption_kwh'),
        ];

        return [
            'report' => $report,
            'data' => $data,
            'summary' => $summary,
        ];
    }

    public function scheduleNextRun(Report $report)
    {
        if (!$report->is_scheduled) return;

        $report->update([
            'next_generation' => match ($report->schedule_frequency) {
                'daily' => now()->addDay(),
                'weekly' => now()->addWeek(),
                'monthly' => now()->addMonth(),
            }
        ]);
    }
}

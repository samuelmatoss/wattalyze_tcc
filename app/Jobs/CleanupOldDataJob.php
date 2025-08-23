<?php

namespace App\Jobs;

use App\Models\EnergyConsumption;
use App\Models\Alert;
use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class CleanupOldDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $this->cleanupEnergyData();
        $this->cleanupAlerts();
        $this->cleanupReports();
    }

    protected function cleanupEnergyData()
    {
        $retentionDays = config('wattalyze.retention.raw_data', 30);
        $cutoffDate = now()->subDays($retentionDays);
        
        EnergyConsumption::where('created_at', '<', $cutoffDate)->delete();
    }

    protected function cleanupAlerts()
    {
        $retentionDays = config('wattalyze.retention.alerts', 90);
        $cutoffDate = now()->subDays($retentionDays);
        
        // Manter apenas alertas críticos
        Alert::where('created_at', '<', $cutoffDate)
            ->where('severity', '!=', 'critical')
            ->delete();
    }

    protected function cleanupReports()
    {
        $retentionDays = config('wattalyze.retention.reports', 365);
        $cutoffDate = now()->subDays($retentionDays);
        
        $reports = Report::where('created_at', '<', $cutoffDate)->get();
        
        foreach ($reports as $report) {
            if ($report->file_path && Storage::exists($report->file_path)) {
                Storage::delete($report->file_path);
            }
            $report->delete();
        }
    }
}
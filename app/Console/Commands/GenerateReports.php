<?php
namespace App\Console\Commands;
use App\Jobs\GenerateReportJob;
use App\Models\Report;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
class GenerateReports extends Command
{
    protected $signature = 'reports:generate 
                            {--id= : Generate specific report}
                            {--cleanup : Cleanup old report files}';
    
    protected $description = 'Generate scheduled reports';
    public function handle()
    {
        // Gerar relatórios específicos ou agendados
        if ($id = $this->option('id')) {
            $report = Report::findOrFail($id);
            $this->generateReport($report);
        } else {
            $reports = Report::where('is_scheduled', true)
                ->where('next_generation', '<=', now())
                ->get();
                
            $this->info("Found {$reports->count()} reports to generate.");
            
            foreach ($reports as $report) {
                $this->generateReport($report);
            }
        }
        
        // Limpar arquivos antigos
        if ($this->option('cleanup')) {
            $this->cleanupOldReports();
        }
    }
    
    protected function generateReport(Report $report)
    {
        $this->info("Generating report: {$report->name}");
        GenerateReportJob::dispatch($report);
        $this->info("Report generation job dispatched.");
    }
    
    protected function cleanupOldReports()
    {
        $cutoff = now()->subDays(config('reports.retention_days', 30));
        $reports = Report::where('created_at', '<', $cutoff)->get();
        
        $this->info("Cleaning up {$reports->count()} old reports...");
        $bar = $this->output->createProgressBar($reports->count());
        
        foreach ($reports as $report) {
            if ($report->file_path && Storage::exists($report->file_path)) {
                Storage::delete($report->file_path);
            }
            $report->delete();
            $bar->advance();
        }
        
        $bar->finish();
        $this->info("\nReport cleanup completed.");
    }
}
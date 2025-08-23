<?php

namespace App\Console\Commands;

use App\Jobs\CheckAlertsJob;
use App\Jobs\SendNotificationJob;
use App\Models\Alert;
use App\Models\NotificationLog;
use Illuminate\Console\Command;


class ProcessAlerts extends Command
{
    protected $signature = 'alerts:process 
                            {--resolve : Resolve pending alerts}
                            {--retry-failed : Retry failed notifications}';
    
    protected $description = 'Process all alert rules and notifications';

    public function handle()
    {
        // Processar regras de alerta
        $this->info('Processing alert rules...');
        CheckAlertsJob::dispatch();
        $this->info('Alert processing job dispatched.');
        
        // Resolver alertas pendentes
        if ($this->option('resolve')) {
            $resolved = Alert::where('is_resolved', false)
                ->where('created_at', '<', now()->subHours(24))
                ->update(['is_resolved' => true, 'resolved_at' => now()]);
                
            $this->info("Resolved $resolved old alerts.");
        }
        
        // Reprocessar notificações falhas
        if ($this->option('retry-failed')) {
            $this->retryFailedNotifications();
        }
    }
    
    protected function retryFailedNotifications()
    {
        $failed = NotificationLog::where('status', 'failed')
            ->where('retry_count', '<', 3)
            ->get();
            
        $this->info("Retrying {$failed->count()} failed notifications...");
        $bar = $this->output->createProgressBar($failed->count());
        
        foreach ($failed as $log) {
            try {
                $alert = Alert::find($log->alert_id);
                SendNotificationJob::dispatch($alert, $log->channels);
                
                $log->update([
                    'status' => 'retrying',
                    'retry_count' => $log->retry_count + 1
                ]);
                
            } catch (\Exception $e) {
                $log->update([
                    'error' => $e->getMessage(),
                    'retry_count' => $log->retry_count + 1
                ]);
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->info("\nNotification retry completed.");
    }
}
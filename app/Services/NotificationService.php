<?php

namespace App\Services;

use App\Models\User;
use App\Models\Alert;
use App\Models\Report;
use App\Mail\AlertNotification;
use App\Mail\ReportMail;
use App\Channels\SmsChannel;
use App\Channels\PushNotificationChannel;
use Illuminate\Support\Facades\Mail;


class NotificationService
{
    public function sendAlertNotification(Alert $alert)
    {
        $user = $alert->user;
        $channels = $user->preferences['notification_channels'] ?? ['email'];
        
        foreach ($channels as $channel) {
            $this->sendViaChannel($user, $alert, $channel);
        }
        
        $this->logNotification($alert, $channels);
    }

    protected function sendViaChannel(User $user, Alert $alert, string $channel)
    {
        switch ($channel) {
            case 'email':
                Mail::to($user->email)->queue(new AlertNotification($alert));
                break;
                
        }
    }



    protected function logNotification(Alert $alert, array $channels)
    {
        // Registrar envio no banco de dados
        $alert->notifications()->create([
            'channels' => $channels,
            'status' => 'sent'
        ]);
    }

    public function sendScheduledReports()
    {
        $reports = Report::where('is_scheduled', true)
            ->where('next_generation', '<=', now())
            ->get();
            
        foreach ($reports as $report) {
            $this->sendReport($report);
        }
    }

    protected function sendReport(Report $report)
    {
        $user = $report->user;
        
        if ($report->format === 'pdf') {
            Mail::to($user->email)->send(new ReportMail($report));
        } else {
            // Enviar link para download
        }
        
        $report->update(['last_sent_at' => now()]);
    }
}
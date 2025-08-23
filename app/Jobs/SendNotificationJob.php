<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\AlertTriggered;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use App\Mail\AlertNotification;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $alert;
    protected $channels;

    public function __construct($alert, $channels = ['mail'])
    {
        $this->alert = $alert;
        $this->channels = $channels;
    }

    public function handle()
    {
        $user = User::find($this->alert->user_id);
        
        if (!$user) return;
        
        foreach ($this->channels as $channel) {
            switch ($channel) {
                case 'email':
                    $this->sendEmail($user);
                    break;
                case 'push':
                    $this->sendPush($user);
                    break;
            }
        }
    }

    protected function sendEmail(User $user)
    {
        Mail::to($user->email)->send(new AlertNotification($this->alert));
    }


    protected function sendPush(User $user)
    {
        Notification::send($user, new AlertTriggered($this->alert));
    }
}
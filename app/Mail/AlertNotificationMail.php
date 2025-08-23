<?php

namespace App\Mail;

use App\Models\Alert;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AlertNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $alert;

    public function __construct(Alert $alert)
    {
        $this->alert = $alert;
    }

    public function build()
    {
        return $this->subject($this->alert->title)
                    ->markdown('emails.alert_notification')
                    ->with([
                        'alert' => $this->alert,
                    ]);
    }
}

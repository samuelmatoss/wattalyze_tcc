<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'alert_id', 'channels', 'status', 'error', 'retry_count'
    ];

    public function alert()
    {
        return $this->belongsTo(Alert::class);
    }
}

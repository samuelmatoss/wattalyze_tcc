<?php

namespace App\Listeners;

use App\Events\DeviceOffline;

class CreateOfflineAlert
{
    public function handle(DeviceOffline $event)
    {
        // Lógica para criar alerta de dispositivo offline
    }
}

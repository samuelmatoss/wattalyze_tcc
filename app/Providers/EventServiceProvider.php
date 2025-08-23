<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // Disparado quando novos dados de energia são processados
        \App\Events\EnergyDataProcessed::class => [
            \App\Listeners\CheckForConsumptionSpikes::class,
            \App\Listeners\UpdateDeviceStatus::class,
            \App\Listeners\DetectAnomalies::class,
        ],

        // Eventos específicos
        \App\Events\DeviceOffline::class => [
            \App\Listeners\SendOfflineNotification::class,
            \App\Listeners\CreateOfflineAlert::class,
        ],

        \App\Events\ConsumptionSpike::class => [
            \App\Listeners\SendSpikeNotification::class,
            \App\Listeners\CreateSpikeAlert::class,
        ],

        \App\Events\HighCostAlert::class => [
            \App\Listeners\SendCostNotification::class,
            \App\Listeners\CreateCostAlert::class,
            \App\Listeners\SuggestCostReductions::class,
        ],

        \App\Events\AnomalyDetected::class => [
            \App\Listeners\SendAnomalyNotification::class,
            \App\Listeners\CreateAnomalyAlert::class,
            \App\Listeners\LogAnomalyForAnalysis::class,
        ],
    ];


    public function boot(): void
    {
        //
    }


    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}

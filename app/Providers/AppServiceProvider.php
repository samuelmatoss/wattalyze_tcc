<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Alert;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */

public function boot(): void
{
    // Compartilhar alerts com todas as views
    View::composer('*', function ($view) {
        if (auth()->check()) {
            $alerts = Alert::where('user_id', auth()->id())
                ->where('is_resolved', false)
                ->with(['device', 'environment'])
                ->latest()
                ->take(5)
                ->get();

            $view->with('alerts', $alerts);
        }
    });
}
}

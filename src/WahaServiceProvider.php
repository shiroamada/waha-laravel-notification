<?php

namespace NotificationChannels\Waha;

use Illuminate\Support\ServiceProvider;

class WahaServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(WahaApi::class, function () {
            $config = config('services.waha');

            return new WahaApi($config);
        });
    }

    public function boot()
    {
        // Boot method for Laravel 11+ compatibility
    }
}

<?php

namespace App\Providers;

use App\Services\Location\Contracts\LocationProviderInterface;
use App\Services\Location\Providers\FallbackLocationProvider;
use App\Services\Location\Providers\OverpassLocationProvider;
use App\Services\Location\Providers\TomTomLocationProvider;
use Illuminate\Support\ServiceProvider;

class LocationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(TomTomLocationProvider::class);
        $this->app->singleton(OverpassLocationProvider::class);

        $this->app->bind(LocationProviderInterface::class, function ($app) {
            return new FallbackLocationProvider(
                primaryProvider: $app->make(TomTomLocationProvider::class),
                fallbackProvider: $app->make(OverpassLocationProvider::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        RateLimiter::for('client', function (Request $request) {
            $client = $request->attributes->get('api_client');
            $rpm = $client->rate_per_min ?? 120;
            return Limit::perMinute($rpm)->by('client:' . $client?->id);
        });
    }
}

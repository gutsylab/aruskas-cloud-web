<?php

namespace App\Providers;

use App\Services\TenantService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register TenantService as singleton
        $this->app->singleton(TenantService::class, function ($app) {
            return new TenantService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register tenant model observers
        $this->registerTenantObservers();

        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Console\Commands\MakeGlobalController::class,
                \App\Console\Commands\MakeTenantController::class,
            ]);
        }
    }

    /**
     * Register observers for tenant models.
     */
    private function registerTenantObservers(): void
    {
        // Only register observers in tenant context
        if (request()->attributes->has('tenant')) {
            \App\Models\Tenant\CashAccount::observe(\App\Observers\TenantBaseModelObserver::class);
            \App\Models\Tenant\CashCategory::observe(\App\Observers\TenantBaseModelObserver::class);
            
            // Add more tenant models here as needed
            // \App\Models\Tenant\YourModel::observe(\App\Observers\TenantBaseModelObserver::class);
        }
    }
}

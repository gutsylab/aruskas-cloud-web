<?php

namespace App\Providers;

use App\Models\Tenant\Account;
use App\Models\Tenant\Journal;
use App\Models\Tenant\JournalLine;
use App\Observers\BaseModelObserver;
use App\Services\TenantService;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

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
        // Use custom PersonalAccessToken model for multi-tenant support
        Sanctum::usePersonalAccessTokenModel(\App\Models\Tenant\PersonalAccessToken::class);

        // Register tenant model observers
        $this->registerTenantObservers();

        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Console\Commands\MakeGlobalController::class,
                \App\Console\Commands\MakeTenantController::class,
            ]);
        }

        // Tenant
        Account::observe(BaseModelObserver::class);
        Journal::observe(BaseModelObserver::class);
        JournalLine::observe(BaseModelObserver::class);
    }

    /**
     * Register observers for tenant models.
     */
    private function registerTenantObservers(): void
    {
        // Only register observers in tenant context
        if (request()->attributes->has('tenant')) {

            // Add more tenant models here as needed
            // \App\Models\Tenant\YourModel::observe(\App\Observers\TenantBaseModelObserver::class);
        }
    }
}

<?php

namespace App\Jobs;

use App\Models\Global\Merchant;
use App\Services\TenantService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Jobs\SendTenantAccountSetupComplete;

class SetupTenantDatabase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes
    public $tries = 3;

    protected Merchant $merchant;
    protected string $adminName;
    protected string $adminEmail;
    protected string $adminPassword;

    /**
     * Create a new job instance.
     */
    public function __construct(Merchant $merchant, string $adminName, string $adminEmail, string $adminPassword)
    {
        $this->merchant = $merchant;
        $this->adminName = $adminName;
        $this->adminEmail = $adminEmail;
        $this->adminPassword = $adminPassword;
    }

    /**
     * Execute the job.
     */
    public function handle(TenantService $tenantService): void
    {
        try {
            Log::info("Starting tenant database setup for {$this->merchant->tenant_id}");

            // Create tenant database and run migrations
            $tenantService->createTenant($this->merchant);

            // Create admin user in tenant database
            $tenantService->setTenantConnection($this->merchant);

            \App\Models\Tenant\User::create([
                'name' => $this->adminName,
                'email' => $this->adminEmail,
                'password' => $this->adminPassword, // Already hashed from controller
                'email_verified_at' => null,
            ]);

            $tenantService->resetToGlobalConnection();

            // Seed tenant database with initial data
            Artisan::call('db:seed:tenant', [
                'tenant_id' => $this->merchant->tenant_id,
                '--force' => true,
            ]);

            Log::info("Completed tenant database setup for {$this->merchant->tenant_id}");

            // Send account setup complete email with verification link
            SendTenantAccountSetupComplete::dispatch($this->merchant);
        } catch (\Exception $e) {
            Log::error("Failed to setup tenant database for {$this->merchant->tenant_id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("SetupTenantDatabase job failed for merchant {$this->merchant->id}: " . $exception->getMessage());

        // Optionally update merchant status
        $this->merchant->update(['status' => false]);
    }
}

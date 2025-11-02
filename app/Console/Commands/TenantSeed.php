<?php

namespace App\Console\Commands;

use App\Models\Global\Merchant;
use App\Services\TenantService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class TenantSeed extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'db:seed:tenant
                          {tenant_id? : Tenant ID to seed}
                          {--all : Seed all tenants}
                          {--class= : The class name of the root seeder}
                          {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     */
    protected $description = 'Seed the tenant database(s) with records';

    protected TenantService $tenantService;

    public function __construct(TenantService $tenantService)
    {
        parent::__construct();
        $this->tenantService = $tenantService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if ($this->option('all')) {
            return $this->seedAllTenants();
        }

        $tenantId = $this->argument('tenant_id');
        if (!$tenantId) {
            $this->error('Please provide tenant_id or use --all option');
            return 1;
        }

        return $this->seedTenant($tenantId);
    }

    /**
     * Seed all tenant databases.
     */
    protected function seedAllTenants(): int
    {
        $this->info('Seeding all tenant databases...');

        $merchants = Merchant::where('status', true)->get();

        if ($merchants->isEmpty()) {
            $this->warn('No active tenants found.');
            return 0;
        }

        $successCount = 0;
        $errorCount = 0;

        foreach ($merchants as $merchant) {
            $this->line("Seeding tenant: {$merchant->name} ({$merchant->tenant_id})");
            $result = $this->seedTenant($merchant->tenant_id, false);

            if ($result === 0) {
                $successCount++;
            } else {
                $errorCount++;
            }
        }

        $this->info("Seeding completed! Success: {$successCount}, Errors: {$errorCount}");
        return $errorCount > 0 ? 1 : 0;
    }

    /**
     * Seed a specific tenant database.
     */
    protected function seedTenant(string $tenantId, bool $showInfo = true): int
    {
        try {
            $merchant = Merchant::where('tenant_id', $tenantId)->first();

            if (!$merchant) {
                $this->error("Tenant with ID '{$tenantId}' not found.");
                return 1;
            }

            if ($showInfo) {
                $this->info("Seeding tenant: {$merchant->name} ({$merchant->tenant_id})");
            }

            // Set tenant connection
            $this->tenantService->setTenantConnection($merchant);
            $connectionName = $this->tenantService->getTenantConnectionName($merchant);

            $options = [
                '--database' => $connectionName,
                '--force' => $this->option('force'),
            ];

            // If class is specified, use it
            if ($this->option('class')) {
                $options['--class'] = $this->option('class');
                if ($showInfo) {
                    $this->info("Using seeder class: {$this->option('class')}");
                }
            }

            Artisan::call('db:seed', $options);

            if ($showInfo) {
                $this->info("✓ Tenant {$merchant->tenant_id} seeded successfully!");
            }

            // Reset to global connection
            $this->tenantService->resetToGlobalConnection();

            return 0;

        } catch (\Exception $e) {
            $this->error("Error seeding tenant {$tenantId}: " . $e->getMessage());

            // Reset to global connection on error
            $this->tenantService->resetToGlobalConnection();

            return 1;
        }
    }
}

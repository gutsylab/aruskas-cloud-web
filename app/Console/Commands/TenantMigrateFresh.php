<?php

namespace App\Console\Commands;

use App\Models\Global\Merchant;
use App\Services\TenantService;
use Illuminate\Console\Command;

class TenantMigrateFresh extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tenant:migrate-fresh
                          {tenant_id? : Tenant ID to migrate}
                          {--all : Migrate all tenants}
                          {--seed : Seed database after migration}
                          {--force : Force migration in production}';

    /**
     * The console command description.
     */
    protected $description = 'Drop all tables and re-run all migrations for tenant database(s)';

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
            return $this->migrateFreshAllTenants();
        }

        $tenantId = $this->argument('tenant_id');
        if (!$tenantId) {
            $this->error('Please provide tenant_id or use --all option');
            return 1;
        }

        return $this->migrateFreshTenant($tenantId);
    }

    /**
     * Migrate fresh all tenant databases.
     */
    protected function migrateFreshAllTenants(): int
    {
        $this->info('Running fresh migrations for all tenant databases...');

        $merchants = Merchant::where('status', true)->get();

        if ($merchants->isEmpty()) {
            $this->warn('No active tenants found.');
            return 0;
        }

        $successCount = 0;
        $errorCount = 0;

        foreach ($merchants as $merchant) {
            $this->line("Migrating fresh tenant: {$merchant->name} ({$merchant->tenant_id})");
            $result = $this->migrateFreshTenant($merchant->tenant_id, false);

            if ($result === 0) {
                $successCount++;
            } else {
                $errorCount++;
            }
        }

        $this->info("Fresh migration completed! Success: {$successCount}, Errors: {$errorCount}");
        return $errorCount > 0 ? 1 : 0;
    }

    /**
     * Migrate fresh a specific tenant database.
     */
    protected function migrateFreshTenant(string $tenantId, bool $showInfo = true): int
    {
        try {
            $merchant = Merchant::where('tenant_id', $tenantId)->first();

            if (!$merchant) {
                $this->error("Tenant with ID '{$tenantId}' not found.");
                return 1;
            }

            if ($showInfo) {
                $this->info("Running fresh migration for tenant: {$merchant->name} ({$merchant->tenant_id})");
            }

            // Set tenant connection
            $this->tenantService->setTenantConnection($merchant);
            $connectionName = $this->tenantService->getTenantConnectionName($merchant);

            $options = [
                '--database' => $connectionName,
                '--path' => 'database/migrations/tenant',
            ];

            if ($this->option('force')) {
                $options['--force'] = true;
            }

            // Run migrate:fresh
            $this->call('migrate:fresh', $options);

            // Seed if requested
            if ($this->option('seed')) {
                $this->call('db:seed:tenant', [
                    'tenant_id' => $tenantId,
                    '--force' => true,
                ]);
            }

            if ($showInfo) {
                $this->info("✓ Tenant {$merchant->tenant_id} fresh migration completed successfully!");
            }

            // Reset to global connection
            $this->tenantService->resetToGlobalConnection();

            return 0;

        } catch (\Exception $e) {
            $this->error("Error in fresh migration for tenant {$tenantId}: " . $e->getMessage());

            // Reset to global connection on error
            $this->tenantService->resetToGlobalConnection();

            return 1;
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Global\Merchant;
use App\Services\TenantService;
use Illuminate\Console\Command;

class TenantMigrate extends Command
{
    protected $signature = 'tenant:migrate 
                          {tenant_id? : Tenant ID to migrate} 
                          {--all : Migrate all tenants} 
                          {--fresh : Drop all tables and re-run migrations} 
                          {--seed : Seed database after migration}
                          {--force : Force migration in production}';
    protected $description = 'Run migrations for tenant database(s)';

    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        parent::__construct();
        $this->tenantService = $tenantService;
    }

    public function handle()
    {
        if ($this->option('all')) {
            return $this->migrateAllTenants();
        }

        $tenantId = $this->argument('tenant_id');
        if (!$tenantId) {
            $this->error('Please provide tenant_id or use --all option');
            return 1;
        }

        return $this->migrateTenant($tenantId);
    }

    protected function migrateAllTenants()
    {
        $this->info('Migrating all tenant databases...');
        
        $merchants = Merchant::where('status', true)->get();
        
        if ($merchants->isEmpty()) {
            $this->warn('No active tenants found.');
            return 0;
        }

        $successCount = 0;
        $errorCount = 0;

        foreach ($merchants as $merchant) {
            $this->line("Migrating tenant: {$merchant->name} ({$merchant->tenant_id})");
            $result = $this->migrateTenant($merchant->tenant_id, false);
            
            if ($result === 0) {
                $successCount++;
            } else {
                $errorCount++;
            }
        }

        $this->info("Migration completed! Success: {$successCount}, Errors: {$errorCount}");
        return $errorCount > 0 ? 1 : 0;
    }

    protected function migrateTenant($tenantId, $showInfo = true)
    {
        try {
            $merchant = Merchant::where('tenant_id', $tenantId)->first();
            
            if (!$merchant) {
                $this->error("Tenant with ID '{$tenantId}' not found.");
                return 1;
            }

            if ($showInfo) {
                $this->info("Migrating tenant: {$merchant->name} ({$merchant->tenant_id})");
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

            if ($this->option('fresh')) {
                $this->call('migrate:fresh', $options);
            } else {
                $this->call('migrate', $options);
            }

            if ($this->option('seed')) {
                $this->call('db:seed', [
                    '--database' => $connectionName,
                    '--force' => true,
                ]);
            }

            if ($showInfo) {
                $this->info("✓ Tenant {$merchant->tenant_id} migrated successfully!");
            }

            // Reset to global connection
            $this->tenantService->resetToGlobalConnection();

            return 0;

        } catch (\Exception $e) {
            $this->error("Error migrating tenant {$tenantId}: " . $e->getMessage());
            
            // Reset to global connection on error
            $this->tenantService->resetToGlobalConnection();
            
            return 1;
        }
    }
}

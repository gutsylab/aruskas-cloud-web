<?php

namespace App\Console\Commands;

use App\Models\Global\Merchant;
use App\Services\TenantService;
use Illuminate\Console\Command;

class TenantMigrationRollback extends Command
{
    protected $signature = 'tenant:migrate-rollback 
                          {tenant_id? : Tenant ID to rollback} 
                          {--all : Rollback all tenants}
                          {--step= : Number of migrations to rollback}
                          {--batch= : Batch number to rollback}
                          {--force : Force rollback in production}';
    protected $description = 'Rollback tenant database migrations';

    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        parent::__construct();
        $this->tenantService = $tenantService;
    }

    public function handle()
    {
        if ($this->option('all')) {
            if (!$this->confirm('This will rollback migrations for ALL tenants. Are you sure?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
            return $this->rollbackAllTenants();
        }

        $tenantId = $this->argument('tenant_id');
        if (!$tenantId) {
            $this->error('Please provide tenant_id or use --all option');
            return 1;
        }

        return $this->rollbackTenant($tenantId);
    }

    protected function rollbackAllTenants()
    {
        $this->info('Rolling back migrations for all tenant databases...');
        
        $merchants = Merchant::where('status', true)->get();
        
        if ($merchants->isEmpty()) {
            $this->warn('No active tenants found.');
            return 0;
        }

        $successCount = 0;
        $errorCount = 0;

        foreach ($merchants as $merchant) {
            $this->line("Rolling back tenant: {$merchant->name} ({$merchant->tenant_id})");
            $result = $this->rollbackTenant($merchant->tenant_id, false);
            
            if ($result === 0) {
                $successCount++;
            } else {
                $errorCount++;
            }
        }

        $this->info("Rollback completed! Success: {$successCount}, Errors: {$errorCount}");
        return $errorCount > 0 ? 1 : 0;
    }

    protected function rollbackTenant($tenantId, $showInfo = true)
    {
        try {
            $merchant = Merchant::where('tenant_id', $tenantId)->first();
            
            if (!$merchant) {
                $this->error("Tenant with ID '{$tenantId}' not found.");
                return 1;
            }

            if ($showInfo) {
                $this->info("Rolling back migrations for tenant: {$merchant->name} ({$merchant->tenant_id})");
            }

            // Set tenant connection
            $this->tenantService->setTenantConnection($merchant);
            $connectionName = $this->tenantService->getTenantConnectionName($merchant);

            $options = [
                '--database' => $connectionName,
                '--path' => 'database/migrations/tenant',
            ];

            if ($this->option('step')) {
                $options['--step'] = $this->option('step');
            }
            if ($this->option('batch')) {
                $options['--batch'] = $this->option('batch');
            }
            if ($this->option('force')) {
                $options['--force'] = true;
            }

            // Run rollback
            $this->call('migrate:rollback', $options);

            if ($showInfo) {
                $this->info("✓ Tenant {$merchant->tenant_id} migrations rolled back successfully!");
            }

            // Reset to global connection
            $this->tenantService->resetToGlobalConnection();

            return 0;

        } catch (\Exception $e) {
            $this->error("Error rolling back migrations for tenant {$tenantId}: " . $e->getMessage());
            
            // Reset to global connection on error
            $this->tenantService->resetToGlobalConnection();
            
            return 1;
        }
    }
}

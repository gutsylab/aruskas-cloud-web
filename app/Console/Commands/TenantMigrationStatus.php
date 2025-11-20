<?php

namespace App\Console\Commands;

use App\Models\Global\Merchant;
use App\Services\TenantService;
use Illuminate\Console\Command;

class TenantMigrationStatus extends Command
{
    protected $signature = 'tenant:migrate-status 
                          {tenant_id? : Tenant ID to check} 
                          {--all : Check all tenants}';
    protected $description = 'Show the status of tenant database migrations';

    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        parent::__construct();
        $this->tenantService = $tenantService;
    }

    public function handle()
    {
        if ($this->option('all')) {
            return $this->statusAllTenants();
        }

        $tenantId = $this->argument('tenant_id');
        if (!$tenantId) {
            $this->error('Please provide tenant_id or use --all option');
            return 1;
        }

        return $this->statusTenant($tenantId);
    }

    protected function statusAllTenants()
    {
        $this->info('Checking migration status for all tenant databases...');
        $this->line('================================================');
        
        $merchants = Merchant::where('status', true)->get();
        
        if ($merchants->isEmpty()) {
            $this->warn('No active tenants found.');
            return 0;
        }

        foreach ($merchants as $merchant) {
            $this->line("");
            $this->info("Tenant: {$merchant->name} ({$merchant->tenant_id})");
            $this->line("Database: {$merchant->database_name}");
            $this->line("----------------------------------------");
            $this->statusTenant($merchant->tenant_id, false);
        }

        return 0;
    }

    protected function statusTenant($tenantId, $showInfo = true)
    {
        try {
            $merchant = Merchant::where('tenant_id', $tenantId)->first();
            
            if (!$merchant) {
                $this->error("Tenant with ID '{$tenantId}' not found.");
                return 1;
            }

            if ($showInfo) {
                $this->info("Checking migration status for tenant: {$merchant->name} ({$merchant->tenant_id})");
                $this->line("Database: {$merchant->database_name}");
                $this->line("====================================================");
            }

            // Set tenant connection
            $this->tenantService->setTenantConnection($merchant);
            $connectionName = $this->tenantService->getTenantConnectionName($merchant);

            // Get migration status
            $this->call('migrate:status', [
                '--database' => $connectionName,
                '--path' => 'database/migrations/tenant',
            ]);

            // Reset to global connection
            $this->tenantService->resetToGlobalConnection();

            return 0;

        } catch (\Exception $e) {
            $this->error("Error checking migration status for tenant {$tenantId}: " . $e->getMessage());
            
            // Reset to global connection on error
            $this->tenantService->resetToGlobalConnection();
            
            return 1;
        }
    }
}

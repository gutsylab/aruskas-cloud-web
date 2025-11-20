<?php

namespace App\Console\Commands;

use App\Models\Global\Merchant;
use App\Services\TenantService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class TenantQueueWork extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'queue:work-tenant 
                          {tenant_id? : Tenant ID to process queue jobs} 
                          {--all : Process queue jobs for all tenants}
                          {--once : Only process the next job on the queue}
                          {--stop-when-empty : Stop when the queue is empty}
                          {--max-jobs= : The number of jobs to process before stopping}
                          {--max-time= : The maximum number of seconds to run}
                          {--sleep=3 : Number of seconds to sleep when no job is available}
                          {--timeout=60 : The number of seconds a child process can run}
                          {--tries=1 : Number of times to attempt a job before logging it failed}';

    /**
     * The console command description.
     */
    protected $description = 'Process queue jobs for tenant database(s)';

    protected $tenantService;

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
            return $this->processAllTenants();
        }

        $tenantId = $this->argument('tenant_id');
        if (!$tenantId) {
            $this->error('Please provide tenant_id or use --all option');
            return 1;
        }

        return $this->processTenant($tenantId);
    }

    /**
     * Process queue jobs for all tenants.
     */
    protected function processAllTenants(): int
    {
        $this->info('Processing queue jobs for all tenant databases...');
        
        $merchants = Merchant::where('status', true)->get();
        
        if ($merchants->isEmpty()) {
            $this->warn('No active tenants found.');
            return 0;
        }

        $successCount = 0;
        $errorCount = 0;

        foreach ($merchants as $merchant) {
            $this->line("Processing queue for tenant: {$merchant->name} ({$merchant->tenant_id})");
            $result = $this->processTenant($merchant->tenant_id, false);
            
            if ($result === 0) {
                $successCount++;
            } else {
                $errorCount++;
            }
        }

        $this->info("Queue processing completed! Success: {$successCount}, Errors: {$errorCount}");
        return $errorCount > 0 ? 1 : 0;
    }

    /**
     * Process queue jobs for a specific tenant.
     */
    protected function processTenant(string $tenantId, bool $showInfo = true): int
    {
        try {
            $merchant = Merchant::where('tenant_id', $tenantId)->first();
            
            if (!$merchant) {
                $this->error("Tenant with ID '{$tenantId}' not found.");
                return 1;
            }

            if ($showInfo) {
                $this->info("Processing queue for tenant: {$merchant->name} ({$merchant->tenant_id})");
            }

            // Get tenant connection name
            $connectionName = $this->tenantService->getTenantConnectionName($merchant);

            // Set tenant connection before processing
            $this->tenantService->setTenantConnection($merchant);

            // Build queue:work options
            $options = [
                '--sleep' => $this->option('sleep'),
                '--timeout' => $this->option('timeout'),
                '--tries' => $this->option('tries'),
            ];

            if ($this->option('once')) {
                $options['--once'] = true;
            }

            if ($this->option('stop-when-empty')) {
                $options['--stop-when-empty'] = true;
            }

            if ($this->option('max-jobs')) {
                $options['--max-jobs'] = $this->option('max-jobs');
            }

            if ($this->option('max-time')) {
                $options['--max-time'] = $this->option('max-time');
            }

            // Process the queue - connection name is the first argument
            $exitCode = Artisan::call('queue:work', [$connectionName] + $options);

            if ($showInfo && $exitCode === 0) {
                $this->info("✓ Tenant {$merchant->tenant_id} queue processed successfully!");
            }

            // Reset to global connection
            $this->tenantService->resetToGlobalConnection();

            return $exitCode;

        } catch (\Exception $e) {
            $this->error("Error processing queue for tenant {$tenantId}: " . $e->getMessage());
            
            // Reset to global connection on error
            $this->tenantService->resetToGlobalConnection();
            
            return 1;
        }
    }
}

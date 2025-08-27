<?php

namespace App\Console\Commands;

use App\Models\Global\Merchant;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MigrateTenantIds extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tenant:migrate-ids 
                          {--dry-run : Show what would be changed without actually changing it}';

    /**
     * The console command description.
     */
    protected $description = 'Generate tenant IDs for existing merchants and update database names';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        
        // Get merchants without tenant_id
        $merchants = Merchant::whereNull('tenant_id')->orWhere('tenant_id', '')->get();
        
        if ($merchants->isEmpty()) {
            $this->info("All merchants already have tenant IDs.");
            return 0;
        }
        
        $this->info("Found {$merchants->count()} merchant(s) without tenant IDs:");
        
        $tableData = [];
        $updates = [];
        
        foreach ($merchants as $merchant) {
            $tenantId = $this->generateUniqueTenantId();
            $oldDbName = $merchant->database_name;
            $newDbName = Merchant::generateDatabaseName($tenantId);
            
            $tableData[] = [
                $merchant->id,
                $merchant->name,
                $merchant->slug,
                $tenantId,
                $oldDbName,
                $newDbName,
            ];
            
            $updates[] = [
                'merchant' => $merchant,
                'tenant_id' => $tenantId,
                'old_db_name' => $oldDbName,
                'new_db_name' => $newDbName,
            ];
        }
        
        $this->table(
            ['ID', 'Name', 'Slug', 'New Tenant ID', 'Old DB Name', 'New DB Name'],
            $tableData
        );
        
        if ($dryRun) {
            $this->info("This was a dry run. No changes were made.");
            return 0;
        }
        
        if (!$this->confirm('Do you want to proceed with updating tenant IDs and database names?')) {
            $this->info("Operation cancelled.");
            return 0;
        }
        
        $this->info("Updating merchant records...");
        
        foreach ($updates as $update) {
            $merchant = $update['merchant'];
            $merchant->tenant_id = $update['tenant_id'];
            $merchant->database_name = $update['new_db_name'];
            $merchant->save();
            
            $this->info("✓ Updated {$merchant->name} (ID: {$merchant->id})");
        }
        
        $this->info("\n🎉 Migration completed successfully!");
        $this->warn("⚠️  IMPORTANT: You need to manually rename the actual databases:");
        
        foreach ($updates as $update) {
            $this->info("- Rename '{$update['old_db_name']}' to '{$update['new_db_name']}'");
        }
        
        return 0;
    }
    
    /**
     * Generate unique tenant ID.
     */
    private function generateUniqueTenantId(): string
    {
        do {
            $tenantId = strtoupper(Str::random(8));
        } while (Merchant::where('tenant_id', $tenantId)->exists());

        return $tenantId;
    }
}

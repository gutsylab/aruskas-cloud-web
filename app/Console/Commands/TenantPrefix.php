<?php

namespace App\Console\Commands;

use App\Models\Global\Merchant;
use Illuminate\Console\Command;

class TenantPrefix extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tenant:prefix 
                          {action : Action to perform (show, change, list)}
                          {--new-prefix= : New prefix to set (required for change action)}
                          {--dry-run : Show what would be changed without actually changing it}';

    /**
     * The console command description.
     */
    protected $description = 'Manage tenant database prefix configuration';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'show':
                return $this->showCurrentPrefix();

            case 'change':
                return $this->changePrefix();

            case 'list':
                return $this->listTenantDatabases();

            default:
                $this->error("Invalid action: {$action}");
                $this->info("Available actions: show, change, list");
                return 1;
        }
    }

    /**
     * Show current prefix configuration.
     */
    protected function showCurrentPrefix(): int
    {
        $currentPrefix = config('database.tenant.prefix', 'tenant_');
        $separator = config('database.tenant.separator', '_');

        $this->info("Current Tenant Database Configuration:");
        $this->table(
            ['Setting', 'Value'],
            [
                ['Prefix', $currentPrefix],
                ['Separator', $separator],
                ['Example Database Name', $currentPrefix . $separator . 'ABC12345'],
            ]
        );

        return 0;
    }

    /**
     * Change prefix (would require database renaming - dangerous operation).
     */
    protected function changePrefix(): int
    {
        $newPrefix = $this->option('new-prefix');
        $dryRun = $this->option('dry-run');

        if (!$newPrefix) {
            $this->error("New prefix is required for change action");
            $this->info("Use: --new-prefix=your_new_prefix_");
            return 1;
        }

        $currentPrefix = config('database.tenant.prefix', 'tenant_');
        
        if ($newPrefix === $currentPrefix) {
            $this->info("New prefix is the same as current prefix. No changes needed.");
            return 0;
        }

        $this->warn("⚠️  DANGER: Changing tenant prefix is a destructive operation!");
        $this->warn("This would require renaming all existing tenant databases.");
        $this->warn("Current prefix: {$currentPrefix}");
        $this->warn("New prefix: {$newPrefix}");

        // Get all merchants
        $merchants = Merchant::all();
        
        if ($merchants->isEmpty()) {
            $this->info("No tenants found. Safe to change prefix in .env file.");
            $this->info("Update DB_TENANT_PREFIX={$newPrefix} in your .env file");
            return 0;
        }

        $this->info("Found {$merchants->count()} tenant(s) that would be affected:");
        
        $tableData = [];
        foreach ($merchants as $merchant) {
            $currentDbName = $merchant->database_name;
            $separator = config('database.tenant.separator', '_');
            $newDbName = $newPrefix . $separator . $merchant->tenant_id;
            
            $tableData[] = [
                $merchant->name,
                $merchant->slug,
                $merchant->tenant_id ?? 'N/A',
                $currentDbName,
                $newDbName,
            ];
        }

        $this->table(
            ['Merchant Name', 'Slug', 'Tenant ID', 'Current DB Name', 'New DB Name'],
            $tableData
        );

        if ($dryRun) {
            $this->info("This was a dry run. No changes were made.");
            return 0;
        }

        $this->error("❌ Automatic database renaming is not implemented for safety reasons.");
        $this->info("To change the prefix:");
        $this->info("1. Backup all tenant databases");
        $this->info("2. Manually rename databases using your database management tool");
        $this->info("3. Update the database_name field in merchants table");
        $this->info("4. Update DB_TENANT_PREFIX in .env file");

        return 1;
    }

    /**
     * List all tenant databases.
     */
    protected function listTenantDatabases(): int
    {
        $merchants = Merchant::all();
        
        if ($merchants->isEmpty()) {
            $this->info("No tenants found.");
            return 0;
        }

        $currentPrefix = config('database.tenant.prefix', 'tenant_');
        
        $this->info("Current tenant databases (prefix: {$currentPrefix}):");
        
        $tableData = [];
        foreach ($merchants as $merchant) {
            $tableData[] = [
                $merchant->id,
                $merchant->name,
                $merchant->slug,
                $merchant->tenant_id ?? 'N/A',
                $merchant->database_name,
                $merchant->status ? '✅ Active' : '❌ Inactive',
                $merchant->created_at->format('Y-m-d'),
            ];
        }

        $this->table(
            ['ID', 'Name', 'Slug', 'Tenant ID', 'Database Name', 'Status', 'Created'],
            $tableData
        );

        return 0;
    }
}

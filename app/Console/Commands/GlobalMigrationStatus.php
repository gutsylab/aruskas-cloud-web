<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GlobalMigrationStatus extends Command
{
    protected $signature = 'migrate:global-status';
    protected $description = 'Show the status of global database migrations';

    public function handle()
    {
        $this->info('Global Database Migration Status');
        $this->line('=====================================');

        try {
            // Set connection to global
            $originalConnection = config('database.default');
            config(['database.default' => 'global_mysql']);
            
            // Get migration status for global migrations only
            $this->call('migrate:status', [
                '--path' => 'database/migrations/global'
            ]);
            
            // Reset to original connection
            config(['database.default' => $originalConnection]);
            
        } catch (\Exception $e) {
            $this->error('Error checking global migration status: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}

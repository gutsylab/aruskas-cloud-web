<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GlobalMigrationReset extends Command
{
    protected $signature = 'migrate:global-reset';
    protected $description = 'Reset global database migrations (rollback all migrations)';

    public function handle()
    {
        if (!$this->confirm('This will rollback ALL global migrations. Are you sure?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $this->info('Resetting global database migrations...');

        try {
            // Set connection to global
            $originalConnection = config('database.default');
            config(['database.default' => 'global_mysql']);
            
            // Run reset
            $this->call('migrate:reset', [
                '--path' => 'database/migrations/global'
            ]);
            
            // Reset to original connection
            config(['database.default' => $originalConnection]);
            
            $this->info('Global database migrations reset successfully!');
            
        } catch (\Exception $e) {
            $this->error('Error resetting global migrations: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}

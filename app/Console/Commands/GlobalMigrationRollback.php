<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GlobalMigrationRollback extends Command
{
    protected $signature = 'migrate:global-rollback {--step= : Number of migrations to rollback} {--batch= : Batch number to rollback}';
    protected $description = 'Rollback global database migrations';

    public function handle()
    {
        $this->info('Rolling back global database migrations...');

        try {
            // Set connection to global
            $originalConnection = config('database.default');
            config(['database.default' => 'global_mysql']);
            
            $options = ['--path' => 'database/migrations/global'];
            if ($this->option('step')) {
                $options['--step'] = $this->option('step');
            }
            if ($this->option('batch')) {
                $options['--batch'] = $this->option('batch');
            }
            
            // Run rollback
            $this->call('migrate:rollback', $options);
            
            // Reset to original connection
            config(['database.default' => $originalConnection]);
            
            $this->info('Global database migrations rolled back successfully!');
            
        } catch (\Exception $e) {
            $this->error('Error rolling back global migrations: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}

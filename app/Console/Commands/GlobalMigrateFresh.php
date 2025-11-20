<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class GlobalMigrateFresh extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'migrate:global-fresh
                          {--seed : Seed database after migration}
                          {--force : Force migration in production}';

    /**
     * The console command description.
     */
    protected $description = 'Drop all tables and re-run all migrations for the global database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $globalConnection = config('database.global');

        if (!$globalConnection) {
            $this->error('Global database connection not configured');
            return 1;
        }

        $this->info("Running fresh migrations for global database on connection: {$globalConnection}");

        try {
            $this->info('Dropping all tables and re-running migrations...');

            $options = [
                '--database' => $globalConnection,
                '--path' => 'database/migrations/global',
            ];

            if ($this->option('force')) {
                $options['--force'] = true;
            }

            Artisan::call('migrate:fresh', $options);

            $this->info('✓ Global fresh migrations completed successfully');

            // Seed if requested
            if ($this->option('seed')) {
                $this->info('Seeding global database...');
                Artisan::call('db:seed:global', [
                    '--force' => true,
                ]);
                $this->info('✓ Global database seeded successfully');
            }

            return 0;
        } catch (\Exception $e) {
            $this->error("Fresh migration failed: " . $e->getMessage());
            return 1;
        }
    }
}

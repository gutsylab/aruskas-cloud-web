<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class GlobalMigrate extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'migrate:global {--fresh : Drop all tables and re-run all migrations}';

    /**
     * The console command description.
     */
    protected $description = 'Run migrations for the global database';

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

        $this->info("Running global migrations on connection: {$globalConnection}");

        try {
            if ($this->option('fresh')) {
                $this->info('Dropping all tables and re-running migrations...');
                Artisan::call('migrate:fresh', [
                    '--database' => $globalConnection,
                    '--path' => 'database/migrations/global',
                    '--force' => true,
                ]);
            } else {
                Artisan::call('migrate', [
                    '--database' => $globalConnection,
                    '--path' => 'database/migrations/global',
                    '--force' => true,
                ]);
            }

            $this->info('✓ Global migrations completed successfully');

            // Ask if user wants to seed the global database
            if ($this->confirm('Do you want to seed the global database?')) {
                $this->info('Seeding global database...');
                Artisan::call('db:seed', [
                    '--database' => $globalConnection,
                    '--class' => 'SubscriptionPlanSeeder',
                    '--force' => true,
                ]);
                $this->info('✓ Global database seeded successfully');
            }

            return 0;
        } catch (\Exception $e) {
            $this->error("Migration failed: " . $e->getMessage());
            return 1;
        }
    }
}

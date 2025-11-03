<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class GlobalSeed extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'db:seed:global
                          {--class= : The class name of the root seeder}
                          {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     */
    protected $description = 'Seed the global database with records';

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

        $this->info("Seeding global database on connection: {$globalConnection}");

        try {
            $options = [
                '--database' => $globalConnection,
                '--force' => $this->option('force'),
            ];

            // If class is specified, use it
            if ($this->option('class')) {
                $seederClass = $this->option('class');

                // If the class doesn't have a namespace, prepend Database\Seeders\Global\
                if (!str_contains($seederClass, '\\')) {
                    $seederClass = 'Database\\Seeders\\Global\\' . $seederClass;
                }

                $options['--class'] = $seederClass;
                $this->info("Using seeder class: {$seederClass}");
            } else {
                // Default to GlobalSeeder for global database
                $options['--class'] = 'Database\\Seeders\\Global\\GlobalSeeder';
                $this->info('Using default seeder: Database\\Seeders\\Global\\GlobalSeeder');
            }

            Artisan::call('db:seed', $options);

            $this->info('✓ Global database seeded successfully');

            return 0;
        } catch (\Exception $e) {
            $this->error("Seeding failed: " . $e->getMessage());
            return 1;
        }
    }
}

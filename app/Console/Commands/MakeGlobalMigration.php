<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeGlobalMigration extends Command
{
    protected $signature = 'make:migration-global 
                          {name : The name of the migration}
                          {--create= : The table to be created}
                          {--table= : The table to modify}';
    protected $description = 'Create a new migration file for global database';

    public function handle()
    {
        $name = $this->argument('name');
        
        // Add global prefix to avoid confusion
        if (!Str::startsWith($name, 'global_')) {
            $name = 'global_' . $name;
        }

        $arguments = ['name' => $name];
        
        if ($this->option('create')) {
            $arguments['--create'] = $this->option('create');
        }
        
        if ($this->option('table')) {
            $arguments['--table'] = $this->option('table');
        }

        // Create migration in global path
        $arguments['--path'] = 'database/migrations/global';

        $this->call('make:migration', $arguments);

        $this->info("✓ Global migration created: {$name}");
        $this->line("  Location: database/migrations/global/");
        $this->line("  To run: php artisan migrate:global");
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeTenantMigration extends Command
{
    protected $signature = 'make:migration-tenant 
                          {name : The name of the migration}
                          {--create= : The table to be created}
                          {--table= : The table to modify}';
    protected $description = 'Create a new migration file for tenant databases';

    public function handle()
    {
        $name = $this->argument('name');
        
        // Add tenant prefix to make it clear
        if (!Str::startsWith($name, 'tenant_')) {
            $name = 'tenant_' . $name;
        }

        $arguments = ['name' => $name];
        
        if ($this->option('create')) {
            $arguments['--create'] = $this->option('create');
        }
        
        if ($this->option('table')) {
            $arguments['--table'] = $this->option('table');
        }

        // Create migration in tenant path
        $arguments['--path'] = 'database/migrations/tenant';

        $this->call('make:migration', $arguments);

        $this->info("✓ Tenant migration created: {$name}");
        $this->line("  Location: database/migrations/tenant/");
        $this->line("  To run for one tenant: php artisan tenant:migrate TENANT_ID");
        $this->line("  To run for all tenants: php artisan tenant:migrate --all");
    }
}

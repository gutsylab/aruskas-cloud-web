<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeTenantController extends Command
{
    protected $signature = 'make:controller-tenant 
                          {name : The name of the controller}
                          {--r|resource : Generate a resource controller}
                          {--api : Generate an API resource controller}
                          {--invokable : Generate a single method, invokable controller}
                          {--model= : Generate a resource controller for the given model}
                          {--parent= : Generate a nested resource controller for the given parent model}';
    protected $description = 'Create a new controller for tenant application';

    public function handle()
    {
        $name = $this->argument('name');
        
        $arguments = ['name' => $name];

        // Map options
        if ($this->option('resource')) {
            $arguments['--resource'] = true;
        }
        
        if ($this->option('api')) {
            $arguments['--api'] = true;
        }
        
        if ($this->option('invokable')) {
            $arguments['--invokable'] = true;
        }
        
        if ($this->option('model')) {
            $arguments['--model'] = $this->option('model');
        }
        
        if ($this->option('parent')) {
            $arguments['--parent'] = $this->option('parent');
        }

        // Create the controller
        $this->call('make:controller', $arguments);

        // Move controller to Tenant namespace
        $this->moveControllerToTenant($name);

        $this->info("✓ Tenant controller created: App\\Http\\Controllers\\Tenant\\{$name}");
        $this->line("  For tenant routes: Route::middleware(['tenant', 'auth'])->group(function () { ... });");
    }

    private function moveControllerToTenant($controllerName)
    {
        $originalPath = app_path("Http/Controllers/{$controllerName}.php");
        $tenantPath = app_path("Http/Controllers/Tenant/{$controllerName}.php");
        
        if (file_exists($originalPath)) {
            // Ensure Tenant directory exists
            $tenantDir = app_path("Http/Controllers/Tenant");
            if (!is_dir($tenantDir)) {
                mkdir($tenantDir, 0755, true);
            }

            // Read and update the controller content
            $content = file_get_contents($originalPath);
            
            // Update namespace
            $content = str_replace(
                'namespace App\\Http\\Controllers;',
                'namespace App\\Http\\Controllers\\Tenant;',
                $content
            );

            // Add Controller import if not exists
            if (strpos($content, 'use App\\Http\\Controllers\\Controller;') === false) {
                $content = str_replace(
                    'namespace App\\Http\\Controllers\\Tenant;',
                    "namespace App\\Http\\Controllers\\Tenant;\n\nuse App\\Http\\Controllers\\Controller;",
                    $content
                );
            }

            // Update model imports to use Tenant namespace if needed
            $content = preg_replace(
                '/use App\\\\Models\\\\([^;\\\\]+);/',
                'use App\\Models\\Tenant\\$1;',
                $content
            );

            // Write to new location
            file_put_contents($tenantPath, $content);
            
            // Remove original file
            unlink($originalPath);
            
            $this->line("  ✓ Controller moved to: app/Http/Controllers/Tenant/{$controllerName}.php");
        }
    }
}

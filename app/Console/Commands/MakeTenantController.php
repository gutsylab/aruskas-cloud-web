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

        // If the controller name doesn't start with 'Tenant/', don't move it
        if (strpos($controllerName, 'Api/Tenant/') === 0 || strpos($controllerName, 'Tenant/') === 0) {
            $this->line("  ✓ Controller already in Tenant namespace");
            return;
        }

        $tenantPath = app_path("Http/Controllers/Tenant/{$controllerName}.php");

        if (file_exists($originalPath)) {
            // Get the directory path
            $tenantDir = dirname($tenantPath);

            // Ensure directory exists
            if (!is_dir($tenantDir)) {
                mkdir($tenantDir, 0755, true);
            }

            // Read and update the controller content
            $content = file_get_contents($originalPath);

            // Extract namespace from controller name
            $namespaceParts = explode('/', $controllerName);
            array_pop($namespaceParts); // Remove controller name
            $subNamespace = implode('\\', $namespaceParts);

            $oldNamespace = 'App\\Http\\Controllers';
            $newNamespace = 'App\\Http\\Controllers\\Tenant';

            if (!empty($subNamespace)) {
                $oldNamespace .= '\\' . $subNamespace;
                $newNamespace .= '\\' . $subNamespace;
            }

            // Update namespace
            $content = str_replace(
                "namespace {$oldNamespace};",
                "namespace {$newNamespace};",
                $content
            );

            // Add Controller import if not exists
            if (strpos($content, 'use App\\Http\\Controllers\\Controller;') === false) {
                $content = preg_replace(
                    '/namespace (.+);/',
                    "namespace $1;\n\nuse App\\Http\\Controllers\\Controller;",
                    $content,
                    1
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

            // Clean up empty directories
            $originalDir = dirname($originalPath);
            if (is_dir($originalDir) && count(scandir($originalDir)) === 2) {
                rmdir($originalDir);
            }

            $this->line("  ✓ Controller moved to: app/Http/Controllers/Tenant/{$controllerName}.php");
        }
    }
}

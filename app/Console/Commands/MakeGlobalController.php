<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeGlobalController extends Command
{
    protected $signature = 'make:controller-global 
                          {name : The name of the controller}
                          {--r|resource : Generate a resource controller}
                          {--api : Generate an API resource controller}
                          {--invokable : Generate a single method, invokable controller}
                          {--model= : Generate a resource controller for the given model}
                          {--parent= : Generate a nested resource controller for the given parent model}';
    protected $description = 'Create a new controller for global admin';

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

        // Move controller to Global namespace
        $this->moveControllerToGlobal($name);

        $this->info("✓ Global controller created: App\\Http\\Controllers\\Global\\{$name}");
        $this->line("  For admin routes: Route::middleware(['auth:admin'])->group(function () { ... });");
    }

    private function moveControllerToGlobal($controllerName)
    {
        $originalPath = app_path("Http/Controllers/{$controllerName}.php");
        $globalPath = app_path("Http/Controllers/Global/{$controllerName}.php");
        
        if (file_exists($originalPath)) {
            // Ensure Global directory exists
            $globalDir = app_path("Http/Controllers/Global");
            if (!is_dir($globalDir)) {
                mkdir($globalDir, 0755, true);
            }

            // Read and update the controller content
            $content = file_get_contents($originalPath);
            
            // Update namespace
            $content = str_replace(
                'namespace App\\Http\\Controllers;',
                'namespace App\\Http\\Controllers\\Global;',
                $content
            );

            // Add Controller import if not exists
            if (strpos($content, 'use App\\Http\\Controllers\\Controller;') === false) {
                $content = str_replace(
                    'namespace App\\Http\\Controllers\\Global;',
                    "namespace App\\Http\\Controllers\\Global;\n\nuse App\\Http\\Controllers\\Controller;",
                    $content
                );
            }

            // Update model imports to use Global namespace if needed
            $content = preg_replace(
                '/use App\\\\Models\\\\([^;\\\\]+);/',
                'use App\\Models\\Global\\$1;',
                $content
            );

            // Write to new location
            file_put_contents($globalPath, $content);
            
            // Remove original file
            unlink($originalPath);
            
            $this->line("  ✓ Controller moved to: app/Http/Controllers/Global/{$controllerName}.php");
        }
    }
}

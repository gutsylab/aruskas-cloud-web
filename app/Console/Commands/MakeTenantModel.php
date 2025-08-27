<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeTenantModel extends Command
{
    protected $signature = 'make:model-tenant 
                          {name : The name of the model}
                          {--m|migration : Also create a migration file}
                          {--f|factory : Also create a factory}
                          {--s|seeder : Also create a seeder}
                          {--c|controller : Also create a controller}
                          {--r|resource : Also create a resource controller}
                          {--requests : Also create form request classes}
                          {--a|all : Generate all related files}';
    protected $description = 'Create a new Eloquent model for tenant databases';

    public function handle()
    {
        $name = $this->argument('name');
        
        $arguments = ['name' => "Tenant/{$name}"];

        // Map options
        $mappedOptions = [
            'factory' => 'factory',
            'seeder' => 'seeder', 
            'controller' => 'controller',
            'resource' => 'resource',
            'all' => 'all'
        ];

        foreach ($mappedOptions as $option => $flag) {
            if ($this->option($option)) {
                $arguments["--{$flag}"] = true;
            }
        }

        if ($this->option('migration')) {
            $arguments['--migration'] = true;
        }

        if ($this->option('requests')) {
            $arguments['--requests'] = true;
        }

        // Create the model
        $this->call('make:model', $arguments);

        // If migration was requested, move it to tenant path
        if ($this->option('migration') || $this->option('all')) {
            $this->info("✓ Moving migration to tenant directory...");
            $this->moveLatestMigrationToTenant($name);
        }

        // If controller was requested, move it to tenant namespace
        if ($this->option('controller') || $this->option('resource') || $this->option('all')) {
            $this->moveControllerToTenant($name);
        }

        // Add BelongsToTenant trait to the model
        $this->addBelongsToTenantTrait($name);

        $this->info("✓ Tenant model created: App\\Models\\Tenant\\{$name}");
        $this->line("  ✓ BelongsToTenant trait added automatically");
        $this->line("  To run migration: php artisan tenant:migrate --all");
    }

    private function moveLatestMigrationToTenant($modelName)
    {
        $migrationsPath = database_path('migrations');
        $tenantPath = database_path('migrations/tenant');
        
        // Ensure tenant directory exists
        if (!is_dir($tenantPath)) {
            mkdir($tenantPath, 0755, true);
        }

        $tableName = Str::snake(Str::pluralStudly($modelName));
        
        // Find the latest migration for this model
        $files = glob($migrationsPath . "/*create_{$tableName}_table.php");
        
        if (!empty($files)) {
            $latestFile = end($files);
            $filename = basename($latestFile);
            $newPath = $tenantPath . '/' . $filename;
            
            if (rename($latestFile, $newPath)) {
                $this->line("  ✓ Migration moved to: database/migrations/tenant/{$filename}");
            } else {
                $this->warn("  ⚠ Could not move migration file automatically");
                $this->line("  Please move manually: {$filename}");
            }
        }
    }

    private function addBelongsToTenantTrait($modelName)
    {
        $modelPath = app_path("Models/Tenant/{$modelName}.php");
        
        if (!file_exists($modelPath)) {
            $this->warn("  ⚠ Model file not found: {$modelPath}");
            return;
        }

        $content = file_get_contents($modelPath);
        
        // Check if trait is already added
        if (strpos($content, 'use BelongsToTenant;') !== false) {
            $this->line("  ✓ BelongsToTenant trait already exists");
            return;
        }

        // Add use statement
        if (strpos($content, 'use App\\Traits\\BelongsToTenant;') === false) {
            $content = str_replace(
                'use Illuminate\\Database\\Eloquent\\Model;',
                "use Illuminate\\Database\\Eloquent\\Model;\nuse App\\Traits\\BelongsToTenant;",
                $content
            );
        }

        // Add trait to class
        $content = preg_replace(
            '/(class\s+' . $modelName . '\s+extends\s+Model\s*\{)/',
            "$1\n    use BelongsToTenant;\n",
            $content
        );

        file_put_contents($modelPath, $content);
        $this->line("  ✓ BelongsToTenant trait added to model");
    }

    private function moveControllerToTenant($modelName)
    {
        $controllerName = "{$modelName}Controller";
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

            // Update model import if exists
            $content = str_replace(
                "use App\\Models\\{$modelName};",
                "use App\\Models\\Tenant\\{$modelName};",
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

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeGlobalModel extends Command
{
    protected $signature = 'make:model-global 
                          {name : The name of the model}
                          {--m|migration : Also create a migration file}
                          {--f|factory : Also create a factory}
                          {--s|seeder : Also create a seeder}
                          {--c|controller : Also create a controller}
                          {--r|resource : Also create a resource controller}
                          {--requests : Also create form request classes}
                          {--a|all : Generate all related files}';
    protected $description = 'Create a new Eloquent model for global database';

    public function handle()
    {
        $name = $this->argument('name');
        
        $arguments = ['name' => "Global/{$name}"];

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

        // If migration was requested, move it to global path
        if ($this->option('migration') || $this->option('all')) {
            $this->info("✓ Moving migration to global directory...");
            $this->moveLatestMigrationToGlobal($name);
        }

        // If controller was requested, move it to global namespace
        if ($this->option('controller') || $this->option('resource') || $this->option('all')) {
            $this->moveControllerToGlobal($name);
        }

        $this->info("✓ Global model created: App\\Models\\Global\\{$name}");
        $this->line("  Remember to use: use App\\Models\\Global\\{$name};");
    }

    private function moveLatestMigrationToGlobal($modelName)
    {
        $migrationsPath = database_path('migrations');
        $globalPath = database_path('migrations/global');
        
        // Ensure global directory exists
        if (!is_dir($globalPath)) {
            mkdir($globalPath, 0755, true);
        }

        $tableName = Str::snake(Str::pluralStudly($modelName));
        
        // Find the latest migration for this model
        $files = glob($migrationsPath . "/*create_{$tableName}_table.php");
        
        if (!empty($files)) {
            $latestFile = end($files);
            $filename = basename($latestFile);
            $newPath = $globalPath . '/' . $filename;
            
            if (rename($latestFile, $newPath)) {
                $this->line("  ✓ Migration moved to: database/migrations/global/{$filename}");
            } else {
                $this->warn("  ⚠ Could not move migration file automatically");
                $this->line("  Please move manually: {$filename}");
            }
        }
    }

    private function moveControllerToGlobal($modelName)
    {
        $controllerName = "{$modelName}Controller";
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

            // Update model import if exists
            $content = str_replace(
                "use App\\Models\\{$modelName};",
                "use App\\Models\\Global\\{$modelName};",
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

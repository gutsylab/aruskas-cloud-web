<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeGlobalSeeder extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'make:seeder:global {name : The name of the seeder class}';

    /**
     * The console command description.
     */
    protected $description = 'Create a new seeder class for global database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->argument('name');

        // Ensure the name ends with 'Seeder'
        if (!Str::endsWith($name, 'Seeder')) {
            $name .= 'Seeder';
        }

        $path = database_path("seeders/Global/{$name}.php");

        // Check if seeder already exists
        if (File::exists($path)) {
            $this->error("Seeder [{$name}] already exists!");
            return 1;
        }

        // Create directory if it doesn't exist
        $directory = dirname($path);
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Generate seeder content
        $stub = $this->getStub();
        $content = str_replace('{{class}}', $name, $stub);

        // Write file
        File::put($path, $content);

        $this->info("Global seeder [{$name}] created successfully.");
        $this->line("Location: {$path}");

        return 0;
    }

    /**
     * Get the stub file for the seeder.
     */
    protected function getStub(): string
    {
        return <<<'STUB'
<?php

namespace Database\Seeders\Global;

use Illuminate\Database\Seeder;

class {{class}} extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed data for global database

    }
}

STUB;
    }
}

<?php

namespace Database\Seeders\Tenant;

<<<<<<< HEAD
use App\Models\Tenant\User;
use App\Models\Tenant\Account;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
=======
use App\Models\Tenant\Account;
use App\Models\Tenant\User;
use Illuminate\Database\Seeder;
>>>>>>> origin/main

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // if env is production, prevent seeding
<<<<<<< HEAD
        if (app()->environment('production')) {
=======
        if (app()->environment('production') || true) {
>>>>>>> origin/main
            // $this->command->error('Seeding in production environment is not allowed.');
            // return;
        }

        // Seed data for tenant database
<<<<<<< HEAD

        try {
            $this->call(\Database\Seeders\Tenant\Permissions\PermissionsSeeder::class);

            $this->call(\Database\Seeders\Tenant\SequenceSeeder::class);

            $this->call(\Database\Seeders\Tenant\AccountSeeder::class);
        } catch (\Exception $e) {
            Log::error("TenantSeeder: Error calling child seeders - " . $e->getMessage());
            Log::error("TenantSeeder: Stack trace - " . $e->getTraceAsString());
            throw $e;
        }
=======
        $this->call([
            SequenceSeeder::class,
            // Add more tenant seeders here
            AccountSeeder::class
        ]);
>>>>>>> origin/main
    }
}

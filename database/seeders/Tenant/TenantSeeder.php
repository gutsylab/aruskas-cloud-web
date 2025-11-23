<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\User;
use App\Models\Tenant\Account;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // if env is production, prevent seeding
        if (app()->environment('production')) {
            Log::warning("TenantSeeder: Skipping - production environment");
            // $this->command->error('Seeding in production environment is not allowed.');
            // return;
        }

        // Seed data for tenant database
        Log::info("TenantSeeder: About to call child seeders");

        try {
            Log::info("TenantSeeder: Calling PermissionsSeeder");
            $this->call(\Database\Seeders\Tenant\Permissions\PermissionsSeeder::class);
            Log::info("TenantSeeder: PermissionsSeeder call completed");

            Log::info("TenantSeeder: Calling SequenceSeeder");
            $this->call(\Database\Seeders\Tenant\SequenceSeeder::class);
            Log::info("TenantSeeder: SequenceSeeder call completed");

            Log::info("TenantSeeder: Calling AccountSeeder");
            $this->call(\Database\Seeders\Tenant\AccountSeeder::class);
            Log::info("TenantSeeder: AccountSeeder call completed");
        } catch (\Exception $e) {
            Log::error("TenantSeeder: Error calling child seeders - " . $e->getMessage());
            Log::error("TenantSeeder: Stack trace - " . $e->getTraceAsString());
            throw $e;
        }

        Log::info("TenantSeeder: Completed all child seeders");
    }
}

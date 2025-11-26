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
            // $this->command->error('Seeding in production environment is not allowed.');
            // return;
        }

        // Seed data for tenant database

        try {
            $this->call(\Database\Seeders\Tenant\Permissions\PermissionsSeeder::class);

            $this->call(\Database\Seeders\Tenant\SequenceSeeder::class);

            $this->call(\Database\Seeders\Tenant\AccountSeeder::class);
        } catch (\Exception $e) {
            Log::error("TenantSeeder: Error calling child seeders - " . $e->getMessage());
            Log::error("TenantSeeder: Stack trace - " . $e->getTraceAsString());
            throw $e;
        }
    }
}

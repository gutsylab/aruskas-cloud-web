<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\Account;
use App\Models\Tenant\User;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // if env is production, prevent seeding
        if (app()->environment('production') || true) {
            // $this->command->error('Seeding in production environment is not allowed.');
            // return;
        }

        // Seed data for tenant database
        $this->call([
            SequenceSeeder::class,
            // Add more tenant seeders here
            AccountSeeder::class
        ]);
    }
}

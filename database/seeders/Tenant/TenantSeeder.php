<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\Account;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed data for tenant database
        $this->call([
            SequenceSeeder::class,
            // Add more tenant seeders here
            AccountSeeder::class
        ]);
    }
}

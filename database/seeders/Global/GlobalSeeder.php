<?php

namespace Database\Seeders\Global;

use Database\Seeders\SubscriptionPlanSeeder;
use Illuminate\Database\Seeder;

class GlobalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed data for global database
        $this->call([
            SubscriptionPlanSeeder::class,
            // Add more global seeders here
        ]);
    }
}

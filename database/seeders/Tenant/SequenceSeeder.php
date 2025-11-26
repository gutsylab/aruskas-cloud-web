<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
<<<<<<< HEAD
use Illuminate\Support\Facades\Log;
=======
>>>>>>> origin/main

class SequenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed data for tenant database
<<<<<<< HEAD

=======
>>>>>>> origin/main
        Sequence::firstOrCreate(
            ['code' => 'cash_flow_in'],
            [
                'name' => 'Cash Flow In',
                'prefix' => 'CI',
            ]
        );
        Sequence::firstOrCreate(
            ['code' => 'cash_flow_out'],
            [
                'name' => 'Cash Flow Out',
                'prefix' => 'CO',
            ]
        );
        Sequence::firstOrCreate(
            ['code' => 'cash_transfer'],
            [
                'name' => 'Cash Transfer',
                'prefix' => 'TF',
            ]
        );
    }
}

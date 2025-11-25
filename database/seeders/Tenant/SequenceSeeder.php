<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SequenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed data for tenant database

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

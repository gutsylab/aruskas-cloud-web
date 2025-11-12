<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\Account;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1xx - Cash and Bank Accounts

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Account::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $sort = 1;
        Account::firstOrCreate(
            ['code' => '1100'],
            ['name' => 'Kas Tunai', 'type' => 'asset', 'is_cash' => true, 'sort' => $sort]
        );
        Account::firstOrCreate(
            ['code' => '1110'],
            ['name' => 'Bank BCA', 'type' => 'asset', 'is_cash' => true, 'sort' => $sort]
        );
        Account::firstOrCreate(
            ['code' => '1120'],
            ['name' => 'Bank Mandiri', 'type' => 'asset', 'is_cash' => true, 'sort' => $sort]
        );
        Account::firstOrCreate(
            ['code' => '1130'],
            ['name' => 'Bank BNI', 'type' => 'asset', 'is_cash' => true, 'sort' => $sort]
        );
        Account::firstOrCreate(
            ['code' => '1140'],
            ['name' => 'Bank BRI', 'type' => 'asset', 'is_cash' => true, 'sort' => $sort]
        );
        // Account Receivable
        Account::firstOrCreate(
            ['code' => '1200'],
            ['name' => 'Piutang Usaha', 'type' => 'asset', 'is_cash' => false, 'sort' => $sort, 'cash_flow_type' => 'in']
        );

        // 2xx - Liability Accounts
        $sort = 2;
        // Account Payable
        Account::firstOrCreate(
            ['code' => '2100'],
            ['name' => 'Hutang Usaha', 'type' => 'liability', 'is_cash' => false, 'sort' => $sort, 'cash_flow_type' => 'out']
        );
        Account::firstOrCreate(
            ['code' => '2101'],
            ['name' => 'Hutang Pajak', 'type' => 'liability', 'is_cash' => false, 'sort' => $sort, 'cash_flow_type' => 'out']
        );


        // 3xx - Equity Accounts
        $sort = 3;
        Account::firstOrCreate(
            ['code' => '3100'],
            ['name' => 'Modal Pemilik', 'type' => 'equity', 'is_cash' => false, 'sort' => $sort]
        );
        Account::firstOrCreate(
            ['code' => '3101'],
            ['name' => 'Prive / Pengembalian Pemilik', 'type' => 'equity', 'is_cash' => false, 'sort' => $sort]
        );
        Account::firstOrCreate(
            ['code' => '3102'],
            ['name' => 'Laba Ditahan', 'type' => 'equity', 'is_cash' => false, 'sort' => $sort]
        );

        // 4xx - Revenue Accounts
        $sort = 4;
        Account::firstOrCreate(
            ['code' => '4100'],
            ['name' => 'Pendapatan Tunai', 'type' => 'revenue', 'sort' => $sort, 'cash_flow_type' => 'in']
        );
        Account::firstOrCreate(
            ['code' => '4101'],
            ['name' => 'Pendapatan Jasa', 'type' => 'revenue', 'sort' => $sort, 'cash_flow_type' => 'in']
        );
        Account::firstOrCreate(
            ['code' => '4102'],
            ['name' => 'Pendapatan Produk', 'type' => 'revenue', 'sort' => $sort, 'cash_flow_type' => 'in']
        );

        // 6xx - Expense Accounts
        $sort = 6;
        Account::firstOrCreate(
            ['code' => '6100'],
            ['name' => 'Pembelian Tunai', 'type' => 'expense', 'sort' => $sort, 'cash_flow_type' => 'out']
        );
        Account::firstOrCreate(
            ['code' => '6101'],
            ['name' => 'Pembayaran Gaji dan Upah', 'type' => 'expense', 'sort' => $sort, 'cash_flow_type' => 'out']
        );
        Account::firstOrCreate(
            ['code' => '6102'],
            ['name' => 'Biaya Operasional', 'type' => 'expense', 'sort' => $sort, 'cash_flow_type' => 'out']
        );
        Account::firstOrCreate(
            ['code' => '6103'],
            ['name' => 'Biaya Sewa', 'type' => 'expense', 'sort' => $sort, 'cash_flow_type' => 'out']
        );
        Account::firstOrCreate(
            ['code' => '6104'],
            [
                'name' => 'Biaya Utilitas',
                'type' => 'expense',
                'sort' => $sort,
                'cash_flow_type' => 'out',
                'description' => 'Listrik, Air, Internet'
            ]
        );
    }
}

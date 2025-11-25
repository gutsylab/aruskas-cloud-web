<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\Account;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1xx - Cash and Bank Accounts

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Account::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } catch (\Exception $e) {
            Log::error('AccountSeeder: Truncate failed - ' . $e->getMessage());
            // Continue without truncating
        }

        $sort = 1;
        Account::firstOrCreate(
            ['code' => '1101'],
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
            ['code' => '2101'],
            ['name' => 'Hutang Usaha', 'type' => 'liability', 'is_cash' => false, 'sort' => $sort, 'cash_flow_type' => 'out']
        );
        Account::firstOrCreate(
            ['code' => '2102'],
            ['name' => 'Hutang Pajak', 'type' => 'liability', 'is_cash' => false, 'sort' => $sort, 'cash_flow_type' => 'out']
        );


        // 3xx - Equity Accounts
        $sort = 3;
        Account::firstOrCreate(
            ['code' => '3101'],
            ['name' => 'Modal Pemilik', 'type' => 'equity', 'is_cash' => false, 'sort' => $sort]
        );
        Account::firstOrCreate(
            ['code' => '3102'],
            ['name' => 'Prive / Pengembalian Pemilik', 'type' => 'equity', 'is_cash' => false, 'sort' => $sort]
        );
        Account::firstOrCreate(
            ['code' => '3103'],
            ['name' => 'Laba Ditahan', 'type' => 'equity', 'is_cash' => false, 'sort' => $sort]
        );

        // 4xx - Revenue Accounts
        $sort = 4;
        Account::firstOrCreate(
            ['code' => '4101'],
            ['name' => 'Pendapatan Tunai', 'type' => 'revenue', 'sort' => $sort, 'cash_flow_type' => 'in']
        );
        Account::firstOrCreate(
            ['code' => '4102'],
            ['name' => 'Pendapatan Jasa', 'type' => 'revenue', 'sort' => $sort, 'cash_flow_type' => 'in']
        );
        Account::firstOrCreate(
            ['code' => '4103'],
            ['name' => 'Pendapatan Produk', 'type' => 'revenue', 'sort' => $sort, 'cash_flow_type' => 'in']
        );

        // 6xx - Expense Accounts
        $sort = 6;
        Account::firstOrCreate(
            ['code' => '6101'],
            [
                'name' => 'Makanan dan Minuman',
                'type' => 'expense',
                'sort' => $sort,
                'cash_flow_type' => 'out',
            ]
        );
        Account::firstOrCreate(
            ['code' => '6102'],
            [
                'name' => 'Transportasi',
                'type' => 'expense',
                'sort' => $sort,
                'cash_flow_type' => 'out',
            ]
        );
        Account::firstOrCreate(
            ['code' => '6103'],
            [
                'name' => 'BBM',
                'type' => 'expense',
                'sort' => $sort,
                'cash_flow_type' => 'out',
            ]
        );
        Account::firstOrCreate(
            ['code' => '6104'],
            [
                'name' => 'Hiburan',
                'type' => 'expense',
                'sort' => $sort,
                'cash_flow_type' => 'out',
            ]
        );
        Account::firstOrCreate(
            ['code' => '6105'],
            [
                'name' => 'Kesehatan',
                'type' => 'expense',
                'sort' => $sort,
                'cash_flow_type' => 'out',
            ]
        );
        Account::firstOrCreate(
            ['code' => '6106'],
            [
                'name' => 'Alat Kerja / Sekolah',
                'type' => 'expense',
                'sort' => $sort,
                'cash_flow_type' => 'out',
            ]
        );
        Account::firstOrCreate(
            ['code' => '6107'],
            ['name' => 'Pembelian Tunai Lainnya', 'type' => 'expense', 'sort' => $sort, 'cash_flow_type' => 'out']
        );
        //
        Account::firstOrCreate(
            ['code' => '6201'],
            ['name' => 'Biaya Gaji dan Upah', 'type' => 'expense', 'sort' => $sort, 'cash_flow_type' => 'out']
        );
        //
        Account::firstOrCreate(
            ['code' => '6301'],
            ['name' => 'Biaya Operasional', 'type' => 'expense', 'sort' => $sort, 'cash_flow_type' => 'out']
        );
        //
        Account::firstOrCreate(
            ['code' => '6401'],
            ['name' => 'Biaya Sewa Rumah', 'type' => 'expense', 'sort' => $sort, 'cash_flow_type' => 'out']
        );
        Account::firstOrCreate(
            ['code' => '6402'],
            ['name' => 'Biaya Sewa Kos', 'type' => 'expense', 'sort' => $sort, 'cash_flow_type' => 'out']
        );
        Account::firstOrCreate(
            ['code' => '6403'],
            ['name' => 'Biaya Sewa Tenant / Booth', 'type' => 'expense', 'sort' => $sort, 'cash_flow_type' => 'out']
        );
        //
        Account::firstOrCreate(
            ['code' => '6501'],
            [
                'name' => 'Tagihan Listrik',
                'type' => 'expense',
                'sort' => $sort,
                'cash_flow_type' => 'out',
            ]
        );
        Account::firstOrCreate(
            ['code' => '6502'],
            [
                'name' => 'Tagihan Air',
                'type' => 'expense',
                'sort' => $sort,
                'cash_flow_type' => 'out',
            ]
        );
        Account::firstOrCreate(
            ['code' => '6503'],
            [
                'name' => 'Tagihan Internet',
                'type' => 'expense',
                'sort' => $sort,
                'cash_flow_type' => 'out',
            ]
        );
        Account::firstOrCreate(
            ['code' => '6504'],
            [
                'name' => 'Tagihan Streaming',
                'type' => 'expense',
                'sort' => $sort,
                'cash_flow_type' => 'out',
            ]
        );
        Account::firstOrCreate(
            ['code' => '6505'],
            [
                'name' => 'Tagihan Layanan Cloud',
                'type' => 'expense',
                'sort' => $sort,
                'cash_flow_type' => 'out',
            ]
        );
        Account::firstOrCreate(
            ['code' => '6506'],
            [
                'name' => 'Tagihan Layanan AI',
                'type' => 'expense',
                'sort' => $sort,
                'cash_flow_type' => 'out',
            ]
        );
        Account::firstOrCreate(
            ['code' => '6507'],
            [
                'name' => 'Tagihan Domain, Hosting dan VPS',
                'type' => 'expense',
                'sort' => $sort,
                'cash_flow_type' => 'out',
            ]
        );
    }
}

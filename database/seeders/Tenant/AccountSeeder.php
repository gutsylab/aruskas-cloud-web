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
            ['code' => '11101'],
            ['name' => 'Kas Tunai', 'type' => 'asset', 'is_cash' => true, 'sort' => $sort]
        );
        Account::firstOrCreate(
            ['code' => '11201'],
            ['name' => 'Bank BCA', 'type' => 'asset', 'is_cash' => true, 'sort' => $sort]
        );
        Account::firstOrCreate(
            ['code' => '11202'],
            ['name' => 'Bank Mandiri', 'type' => 'asset', 'is_cash' => true, 'sort' => $sort]
        );
        Account::firstOrCreate(
            ['code' => '11203'],
            ['name' => 'Bank BNI', 'type' => 'asset', 'is_cash' => true, 'sort' => $sort]
        );
        Account::firstOrCreate(
            ['code' => '11204'],
            ['name' => 'Bank BRI', 'type' => 'asset', 'is_cash' => true, 'sort' => $sort]
        );
        Account::firstOrCreate(
            ['code' => '11301'],
            ['name' => 'DANA', 'type' => 'asset', 'is_cash' => true, 'sort' => $sort]
        );
        Account::firstOrCreate(
            ['code' => '11302'],
            ['name' => 'OVO', 'type' => 'asset', 'is_cash' => true, 'sort' => $sort]
        );
        Account::firstOrCreate(
            ['code' => '11303'],
            ['name' => 'GoPay', 'type' => 'asset', 'is_cash' => true, 'sort' => $sort]
        );
        Account::firstOrCreate(
            ['code' => '11304'],
            ['name' => 'ShopeePay', 'type' => 'asset', 'is_cash' => true, 'sort' => $sort]
        );
        // Account Receivable
        Account::firstOrCreate(
            ['code' => '12001'],
            ['name' => 'Piutang Usaha', 'type' => 'asset', 'is_cash' => false, 'sort' => $sort, 'cash_flow_type' => 'in']
        );

        // 2xx - Liability Accounts
        $sort = 2;
        // Account Payable
        Account::firstOrCreate(
            ['code' => '21001'],
            ['name' => 'Hutang Usaha', 'type' => 'liability', 'is_cash' => false, 'sort' => $sort, 'cash_flow_type' => 'out']
        );
        Account::firstOrCreate(
            ['code' => '21002'],
            ['name' => 'Hutang Pajak', 'type' => 'liability', 'is_cash' => false, 'sort' => $sort, 'cash_flow_type' => 'out']
        );


        // 3xx - Equity Accounts
        $sort = 3;
        Account::firstOrCreate(
            ['code' => '31001'],
            ['name' => 'Modal Pemilik', 'type' => 'equity', 'is_cash' => false, 'sort' => $sort]
        );
        Account::firstOrCreate(
            ['code' => '31002'],
            ['name' => 'Prive / Pengembalian Pemilik', 'type' => 'equity', 'is_cash' => false, 'sort' => $sort]
        );
        Account::firstOrCreate(
            ['code' => '31003'],
            ['name' => 'Laba Ditahan', 'type' => 'equity', 'is_cash' => false, 'sort' => $sort]
        );

        // 4xx - Revenue Accounts
        $sort = 4;
        Account::firstOrCreate(
            ['code' => '41001'],
            ['name' => 'Pendapatan Tunai', 'type' => 'revenue', 'sort' => $sort, 'cash_flow_type' => 'in']
        );
        Account::firstOrCreate(
            ['code' => '41002'],
            ['name' => 'Pendapatan Jasa', 'type' => 'revenue', 'sort' => $sort, 'cash_flow_type' => 'in']
        );

        // 6xx - Expense Accounts
        $sort = 6;
        Account::firstOrCreate(
            ['code' => '61001'],
            [
                'name' => 'Makanan dan Minuman',
                'type' => 'expense',
                'sort' => $sort,
                'cash_flow_type' => 'out',
            ]
        );
        Account::firstOrCreate(
            ['code' => '61002'],
            [
                'name' => 'Transportasi',
                'type' => 'expense',
                'sort' => $sort,
                'cash_flow_type' => 'out',
            ]
        );
        Account::firstOrCreate(
            ['code' => '61003'],
            [
                'name' => 'BBM',
                'type' => 'expense',
                'sort' => $sort,
                'cash_flow_type' => 'out',
            ]
        );
        Account::firstOrCreate(
            ['code' => '61004'],
            [
                'name' => 'Hiburan',
                'type' => 'expense',
                'sort' => $sort,
                'cash_flow_type' => 'out',
            ]
        );
        Account::firstOrCreate(
            ['code' => '61005'],
            [
                'name' => 'Kesehatan',
                'type' => 'expense',
                'sort' => $sort,
                'cash_flow_type' => 'out',
            ]
        );
        Account::firstOrCreate(
            ['code' => '61006'],
            [
                'name' => 'Alat Kerja / Sekolah',
                'type' => 'expense',
                'sort' => $sort,
                'cash_flow_type' => 'out',
            ]
        );
        Account::firstOrCreate(
            ['code' => '61007'],
            ['name' => 'Pembelian Tunai Lainnya', 'type' => 'expense', 'sort' => $sort, 'cash_flow_type' => 'out']
        );
        //
        Account::firstOrCreate(
            ['code' => '62001'],
            ['name' => 'Biaya Gaji dan Upah', 'type' => 'expense', 'sort' => $sort, 'cash_flow_type' => 'out']
        );
        //
        Account::firstOrCreate(
            ['code' => '63001'],
            ['name' => 'Biaya Operasional', 'type' => 'expense', 'sort' => $sort, 'cash_flow_type' => 'out']
        );
        //
        Account::firstOrCreate(
            ['code' => '64001'],
            ['name' => 'Biaya Sewa Rumah', 'type' => 'expense', 'sort' => $sort, 'cash_flow_type' => 'out']
        );
        Account::firstOrCreate(
            ['code' => '64002'],
            ['name' => 'Biaya Sewa Kos', 'type' => 'expense', 'sort' => $sort, 'cash_flow_type' => 'out']
        );
        Account::firstOrCreate(
            ['code' => '64003'],
            ['name' => 'Biaya Sewa Tenant / Booth', 'type' => 'expense', 'sort' => $sort, 'cash_flow_type' => 'out']
        );
        //
        Account::firstOrCreate(
            ['code' => '65001'],
            [
                'name' => 'Tagihan Listrik',
                'type' => 'expense',
                'sort' => $sort,
                'cash_flow_type' => 'out',
            ]
        );
        Account::firstOrCreate(
            ['code' => '65002'],
            [
                'name' => 'Tagihan Air',
                'type' => 'expense',
                'sort' => $sort,
                'cash_flow_type' => 'out',
            ]
        );
        Account::firstOrCreate(
            ['code' => '65003'],
            [
                'name' => 'Tagihan Internet',
                'type' => 'expense',
                'sort' => $sort,
                'cash_flow_type' => 'out',
            ]
        );
        Account::firstOrCreate(
            ['code' => '65004'],
            [
                'name' => 'Tagihan Streaming',
                'type' => 'expense',
                'sort' => $sort,
                'cash_flow_type' => 'out',
            ]
        );
        Account::firstOrCreate(
            ['code' => '65005'],
            [
                'name' => 'Tagihan Layanan Cloud',
                'type' => 'expense',
                'sort' => $sort,
                'cash_flow_type' => 'out',
            ]
        );
        Account::firstOrCreate(
            ['code' => '65006'],
            [
                'name' => 'Tagihan Layanan AI',
                'type' => 'expense',
                'sort' => $sort,
                'cash_flow_type' => 'out',
            ]
        );
        Account::firstOrCreate(
            ['code' => '65007'],
            [
                'name' => 'Tagihan Domain, Hosting dan VPS',
                'type' => 'expense',
                'sort' => $sort,
                'cash_flow_type' => 'out',
            ]
        );
    }
}

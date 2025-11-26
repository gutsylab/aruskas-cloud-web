<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\Account;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
<<<<<<< HEAD
use Illuminate\Support\Facades\Log;
=======
>>>>>>> origin/main

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1xx - Cash and Bank Accounts

<<<<<<< HEAD
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
=======
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Account::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
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
>>>>>>> origin/main
            ['name' => 'Piutang Usaha', 'type' => 'asset', 'is_cash' => false, 'sort' => $sort, 'cash_flow_type' => 'in']
        );

        // 2xx - Liability Accounts
        $sort = 2;
        // Account Payable
        Account::firstOrCreate(
<<<<<<< HEAD
            ['code' => '21001'],
            ['name' => 'Hutang Usaha', 'type' => 'liability', 'is_cash' => false, 'sort' => $sort, 'cash_flow_type' => 'out']
        );
        Account::firstOrCreate(
            ['code' => '21002'],
=======
            ['code' => '2101'],
            ['name' => 'Hutang Usaha', 'type' => 'liability', 'is_cash' => false, 'sort' => $sort, 'cash_flow_type' => 'out']
        );
        Account::firstOrCreate(
            ['code' => '2102'],
>>>>>>> origin/main
            ['name' => 'Hutang Pajak', 'type' => 'liability', 'is_cash' => false, 'sort' => $sort, 'cash_flow_type' => 'out']
        );


        // 3xx - Equity Accounts
        $sort = 3;
        Account::firstOrCreate(
<<<<<<< HEAD
            ['code' => '31001'],
            ['name' => 'Modal Pemilik', 'type' => 'equity', 'is_cash' => false, 'sort' => $sort]
        );
        Account::firstOrCreate(
            ['code' => '31002'],
            ['name' => 'Prive / Pengembalian Pemilik', 'type' => 'equity', 'is_cash' => false, 'sort' => $sort]
        );
        Account::firstOrCreate(
            ['code' => '31003'],
=======
            ['code' => '3101'],
            ['name' => 'Modal Pemilik', 'type' => 'equity', 'is_cash' => false, 'sort' => $sort]
        );
        Account::firstOrCreate(
            ['code' => '3102'],
            ['name' => 'Prive / Pengembalian Pemilik', 'type' => 'equity', 'is_cash' => false, 'sort' => $sort]
        );
        Account::firstOrCreate(
            ['code' => '3103'],
>>>>>>> origin/main
            ['name' => 'Laba Ditahan', 'type' => 'equity', 'is_cash' => false, 'sort' => $sort]
        );

        // 4xx - Revenue Accounts
        $sort = 4;
        Account::firstOrCreate(
<<<<<<< HEAD
            ['code' => '41001'],
            ['name' => 'Pendapatan Tunai', 'type' => 'revenue', 'sort' => $sort, 'cash_flow_type' => 'in']
        );
        Account::firstOrCreate(
            ['code' => '41002'],
            ['name' => 'Pendapatan Jasa', 'type' => 'revenue', 'sort' => $sort, 'cash_flow_type' => 'in']
        );
        Account::firstOrCreate(
            ['code' => '41003'],
=======
            ['code' => '4101'],
            ['name' => 'Pendapatan Tunai', 'type' => 'revenue', 'sort' => $sort, 'cash_flow_type' => 'in']
        );
        Account::firstOrCreate(
            ['code' => '4102'],
            ['name' => 'Pendapatan Jasa', 'type' => 'revenue', 'sort' => $sort, 'cash_flow_type' => 'in']
        );
        Account::firstOrCreate(
            ['code' => '4103'],
>>>>>>> origin/main
            ['name' => 'Pendapatan Produk', 'type' => 'revenue', 'sort' => $sort, 'cash_flow_type' => 'in']
        );

        // 6xx - Expense Accounts
        $sort = 6;
        Account::firstOrCreate(
<<<<<<< HEAD
            ['code' => '61001'],
=======
            ['code' => '6101'],
>>>>>>> origin/main
            [
                'name' => 'Makanan dan Minuman',
                'type' => 'expense',
                'sort' => $sort,
                'cash_flow_type' => 'out',
            ]
        );
        Account::firstOrCreate(
<<<<<<< HEAD
            ['code' => '61002'],
=======
            ['code' => '6102'],
>>>>>>> origin/main
            [
                'name' => 'Transportasi',
                'type' => 'expense',
                'sort' => $sort,
                'cash_flow_type' => 'out',
            ]
        );
        Account::firstOrCreate(
<<<<<<< HEAD
            ['code' => '61003'],
=======
            ['code' => '6103'],
>>>>>>> origin/main
            [
                'name' => 'BBM',
                'type' => 'expense',
                'sort' => $sort,
                'cash_flow_type' => 'out',
            ]
        );
        Account::firstOrCreate(
<<<<<<< HEAD
            ['code' => '61004'],
=======
            ['code' => '6104'],
>>>>>>> origin/main
            [
                'name' => 'Hiburan',
                'type' => 'expense',
                'sort' => $sort,
                'cash_flow_type' => 'out',
            ]
        );
        Account::firstOrCreate(
<<<<<<< HEAD
            ['code' => '61005'],
=======
            ['code' => '6105'],
>>>>>>> origin/main
            [
                'name' => 'Kesehatan',
                'type' => 'expense',
                'sort' => $sort,
                'cash_flow_type' => 'out',
            ]
        );
        Account::firstOrCreate(
<<<<<<< HEAD
            ['code' => '61006'],
=======
            ['code' => '6106'],
>>>>>>> origin/main
            [
                'name' => 'Alat Kerja / Sekolah',
                'type' => 'expense',
                'sort' => $sort,
                'cash_flow_type' => 'out',
            ]
        );
        Account::firstOrCreate(
<<<<<<< HEAD
            ['code' => '61007'],
=======
            ['code' => '6107'],
>>>>>>> origin/main
            ['name' => 'Pembelian Tunai Lainnya', 'type' => 'expense', 'sort' => $sort, 'cash_flow_type' => 'out']
        );
        //
        Account::firstOrCreate(
<<<<<<< HEAD
            ['code' => '62001'],
=======
            ['code' => '6201'],
>>>>>>> origin/main
            ['name' => 'Biaya Gaji dan Upah', 'type' => 'expense', 'sort' => $sort, 'cash_flow_type' => 'out']
        );
        //
        Account::firstOrCreate(
<<<<<<< HEAD
            ['code' => '63001'],
=======
            ['code' => '6301'],
>>>>>>> origin/main
            ['name' => 'Biaya Operasional', 'type' => 'expense', 'sort' => $sort, 'cash_flow_type' => 'out']
        );
        //
        Account::firstOrCreate(
<<<<<<< HEAD
            ['code' => '64001'],
            ['name' => 'Biaya Sewa Rumah', 'type' => 'expense', 'sort' => $sort, 'cash_flow_type' => 'out']
        );
        Account::firstOrCreate(
            ['code' => '64002'],
            ['name' => 'Biaya Sewa Kos', 'type' => 'expense', 'sort' => $sort, 'cash_flow_type' => 'out']
        );
        Account::firstOrCreate(
            ['code' => '64003'],
=======
            ['code' => '6401'],
            ['name' => 'Biaya Sewa Rumah', 'type' => 'expense', 'sort' => $sort, 'cash_flow_type' => 'out']
        );
        Account::firstOrCreate(
            ['code' => '6402'],
            ['name' => 'Biaya Sewa Kos', 'type' => 'expense', 'sort' => $sort, 'cash_flow_type' => 'out']
        );
        Account::firstOrCreate(
            ['code' => '6403'],
>>>>>>> origin/main
            ['name' => 'Biaya Sewa Tenant / Booth', 'type' => 'expense', 'sort' => $sort, 'cash_flow_type' => 'out']
        );
        //
        Account::firstOrCreate(
<<<<<<< HEAD
            ['code' => '65001'],
=======
            ['code' => '6501'],
>>>>>>> origin/main
            [
                'name' => 'Tagihan Listrik',
                'type' => 'expense',
                'sort' => $sort,
                'cash_flow_type' => 'out',
            ]
        );
        Account::firstOrCreate(
<<<<<<< HEAD
            ['code' => '65002'],
=======
            ['code' => '6502'],
>>>>>>> origin/main
            [
                'name' => 'Tagihan Air',
                'type' => 'expense',
                'sort' => $sort,
                'cash_flow_type' => 'out',
            ]
        );
        Account::firstOrCreate(
<<<<<<< HEAD
            ['code' => '65003'],
=======
            ['code' => '6503'],
>>>>>>> origin/main
            [
                'name' => 'Tagihan Internet',
                'type' => 'expense',
                'sort' => $sort,
                'cash_flow_type' => 'out',
            ]
        );
        Account::firstOrCreate(
<<<<<<< HEAD
            ['code' => '65004'],
=======
            ['code' => '6504'],
>>>>>>> origin/main
            [
                'name' => 'Tagihan Streaming',
                'type' => 'expense',
                'sort' => $sort,
                'cash_flow_type' => 'out',
            ]
        );
        Account::firstOrCreate(
<<<<<<< HEAD
            ['code' => '65005'],
=======
            ['code' => '6505'],
>>>>>>> origin/main
            [
                'name' => 'Tagihan Layanan Cloud',
                'type' => 'expense',
                'sort' => $sort,
                'cash_flow_type' => 'out',
            ]
        );
        Account::firstOrCreate(
<<<<<<< HEAD
            ['code' => '65006'],
=======
            ['code' => '6506'],
>>>>>>> origin/main
            [
                'name' => 'Tagihan Layanan AI',
                'type' => 'expense',
                'sort' => $sort,
                'cash_flow_type' => 'out',
            ]
        );
        Account::firstOrCreate(
<<<<<<< HEAD
            ['code' => '65007'],
=======
            ['code' => '6507'],
>>>>>>> origin/main
            [
                'name' => 'Tagihan Domain, Hosting dan VPS',
                'type' => 'expense',
                'sort' => $sort,
                'cash_flow_type' => 'out',
            ]
        );
    }
}

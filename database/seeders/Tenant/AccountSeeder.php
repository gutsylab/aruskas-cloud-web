<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\Account;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1xx - Cash and Bank Accounts
        $sort = 1;
        Account::firstOrCreate(
            ['code' => '101'],
            ['name' => 'Kas Tunai', 'type' => 'asset', 'is_cash' => true, 'sort' => $sort]
        );
        Account::firstOrCreate(
            ['code' => '102'],
            ['name' => 'Bank BCA', 'type' => 'asset', 'is_cash' => true, 'sort' => $sort]
        );
        Account::firstOrCreate(
            ['code' => '103'],
            ['name' => 'Bank Mandiri', 'type' => 'asset', 'is_cash' => true, 'sort' => $sort]
        );
        Account::firstOrCreate(
            ['code' => '104'],
            ['name' => 'Bank BNI', 'type' => 'asset', 'is_cash' => true, 'sort' => $sort]
        );
        Account::firstOrCreate(
            ['code' => '105'],
            ['name' => 'Bank BRI', 'type' => 'asset', 'is_cash' => true, 'sort' => $sort]
        );

        // 4xx - Revenue Accounts
        $sort = 4;
        Account::firstOrCreate(
            ['code' => '401'],
            ['name' => 'Pendapatan Tunai', 'type' => 'revenue', 'sort' => $sort]
        );
        Account::firstOrCreate(
            ['code' => '402'],
            ['name' => 'Pendapatan Jasa', 'type' => 'revenue', 'sort' => $sort]
        );
        Account::firstOrCreate(
            ['code' => '403'],
            ['name' => 'Penerimaan Piutang', 'type' => 'revenue', 'sort' => $sort]
        );
        Account::firstOrCreate(
            ['code' => '404'],
            ['name' => 'Setoran Modal', 'type' => 'revenue', 'sort' => $sort]
        );

        // 5xx - Expense Accounts
        $sort = 5;
        Account::firstOrCreate(
            ['code' => '501'],
            ['name' => 'Pembelian Tunai', 'type' => 'expense', 'sort' => $sort]
        );
        Account::firstOrCreate(
            ['code' => '502'],
            ['name' => 'Pembayaran Gaji dan Upah', 'type' => 'expense', 'sort' => $sort]
        );
        Account::firstOrCreate(
            ['code' => '503'],
            ['name' => 'Biaya Operasional', 'type' => 'expense', 'sort' => $sort]
        );
        Account::firstOrCreate(
            ['code' => '504'],
            ['name' => 'Pembayaran Hutang', 'type' => 'expense', 'sort' => $sort]
        );
        Account::firstOrCreate(
            ['code' => '505'],
            ['name' => 'Biaya Sewa', 'type' => 'expense', 'sort' => $sort]
        );
        Account::firstOrCreate(
            ['code' => '506'],
            ['name' => 'Biaya Utilitas', 'type' => 'expense', 'sort' => $sort]
        );
        Account::firstOrCreate(
            ['code' => '507'],
            ['name' => 'Pengambilan Pribadi', 'type' => 'expense', 'sort' => $sort]
        );
    }
}

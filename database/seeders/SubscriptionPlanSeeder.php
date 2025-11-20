<?php

namespace Database\Seeders;

use App\Models\Global\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'description' => 'Coba gratis!',
                'price' => 0,
                'billing_cycle' => 'lifetime',
                'trial_days' => 0,
                'status' => true,
                'features' => [
                    'multi_users' => false,
                    'cash_account_management' => false,
                    'cash_category_management' => false,
                    //
                    'contacts' => true,
                    'payable_receivables' => false,
                    //
                    'export' => false,
                    'attachments' => false,
                    //
                    'support' => true,
                ],
                'limits' => [
                    'max_users' => 1,
                    'max_contacts' => 10,
                    'max_transaction_per_month' => 100,
                ],
            ],
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'description' => 'Gunakan fitur dasar untuk usaha kecil Anda.',
                'price' => 19900,
                'billing_cycle' => 'monthly',
                'trial_days' => 14,
                'status' => true,
                'features' => [
                    'multi_users' => true,
                    'cash_account_management' => true,
                    'cash_category_management' => true,
                    //
                    'contacts' => true,
                    'payable_receivables' => false,
                    //
                    'export' => true,
                    'attachments' => true,
                    //
                    'support' => true,
                ],
                'limits' => [
                    'max_users' => 5,
                    'max_contacts' => 50,
                    'max_transaction_per_month' => 2500,
                ],
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'description' => 'Menjalankan bisnis secara profesional dengan fitur yang lebih lengkap.',
                'price' => 39900,
                'billing_cycle' => 'monthly',
                'trial_days' => 14,
                'status' => true,
                'features' => [
                    'multi_users' => true,
                    'cash_account_management' => true,
                    'cash_category_management' => true,
                    //
                    'contacts' => true,
                    'payable_receivables' => true,
                    //
                    'export' => true,
                    'attachments' => true,
                    //
                    'support' => true,
                ],
                'limits' => [
                    'max_users' => 10,
                    'max_contacts' => 100,
                    'max_transaction_per_month' => 5000,
                ],
            ],
        ];

        foreach ($plans as $plan) {
            $slug = $plan['slug'];
            SubscriptionPlan::updateOrCreate(
                ['slug' => $slug],
                $plan
            );
        }
    }
}

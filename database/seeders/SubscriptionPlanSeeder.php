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
                'name' => 'Free Trial',
                'slug' => 'free-trial',
                'description' => 'Try our platform for free',
                'price' => 0,
                'billing_cycle' => 'monthly',
                'trial_days' => 30,
                'status' => true,
                'features' => [
                    'up_to_2_users' => true,
                    'basic_email_support' => true,
                    'basic_analytics' => true,
                    'api_access' => false,
                    'custom_branding' => false,
                ],
                'limits' => [
                    'max_users' => 2,
                    'max_storage' => 512, // 512MB
                    'max_emails' => 100,
                    'max_api_calls' => 0,
                ],
            ],
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'description' => 'Perfect for small businesses getting started',
                'price' => 24900,
                'billing_cycle' => 'monthly',
                'trial_days' => 14,
                'status' => true,
                'features' => [
                    'up_to_5_users' => true,
                    'basic_email_support' => true,
                    'basic_analytics' => true,
                    'api_access' => false,
                    'custom_branding' => false,
                ],
                'limits' => [
                    'max_users' => 5,
                    'max_storage' => 1024, // 1GB in MB
                    'max_emails' => 1000,
                    'max_api_calls' => 0,
                ],
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'description' => 'Ideal for growing businesses with advanced needs',
                'price' => 44900,
                'billing_cycle' => 'monthly',
                'trial_days' => 14,
                'status' => true,
                'features' => [
                    'up_to_25_users' => true,
                    'priority_email_support' => true,
                    'advanced_analytics' => true,
                    'api_access' => true,
                    'custom_branding' => true,
                    'integrations' => true,
                ],
                'limits' => [
                    'max_users' => 25,
                    'max_storage' => 5120, // 5GB in MB
                    'max_emails' => 10000,
                    'max_api_calls' => 10000,
                ],
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::create($plan);
        }
    }
}

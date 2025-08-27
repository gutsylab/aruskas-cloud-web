<?php

namespace App\Console\Commands;

use App\Models\Global\SubscriptionPlan;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateSubscriptionPlan extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'plan:create 
                          {name : The plan name}
                          {price : The plan price}
                          {--slug= : The plan slug (will be generated if not provided)}
                          {--cycle=monthly : Billing cycle (monthly, yearly, lifetime)}
                          {--trial=0 : Trial days}
                          {--description= : Plan description}';

    /**
     * The console command description.
     */
    protected $description = 'Create a new subscription plan';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->argument('name');
        $price = $this->argument('price');
        $slug = $this->option('slug') ?? Str::slug($name);
        $cycle = $this->option('cycle');
        $trialDays = $this->option('trial');
        $description = $this->option('description');

        try {
            $plan = SubscriptionPlan::create([
                'name' => $name,
                'slug' => $slug,
                'description' => $description,
                'price' => $price,
                'billing_cycle' => $cycle,
                'trial_days' => $trialDays,
                'status' => true,
                'features' => [
                    'users' => 5,
                    'storage' => '1GB',
                    'support' => 'email',
                ],
                'limits' => [
                    'max_users' => 5,
                    'max_storage' => 1024, // MB
                    'max_emails' => 1000,
                ],
            ]);

            $this->info("✓ Subscription plan created successfully!");
            $this->table(
                ['Field', 'Value'],
                [
                    ['Name', $plan->name],
                    ['Slug', $plan->slug],
                    ['Price', '$' . $plan->price],
                    ['Billing Cycle', $plan->billing_cycle],
                    ['Trial Days', $plan->trial_days],
                    ['Status', $plan->status ? 'Active' : 'Inactive'],
                ]
            );

            return 0;
        } catch (\Exception $e) {
            $this->error("Failed to create subscription plan: " . $e->getMessage());
            return 1;
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Global\Merchant;
use App\Models\Global\SubscriptionPlan;
use App\Models\Global\MerchantSubscription;
use App\Services\TenantService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateTenant extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tenant:create 
                          {name : The merchant name}
                          {email : The admin email}
                          {--plan=basic : The subscription plan slug}
                          {--password= : Admin password (will be generated if not provided)}';

    /**
     * The console command description.
     */
    protected $description = 'Create a new tenant (merchant) with database and admin user';

    protected TenantService $tenantService;

    public function __construct(TenantService $tenantService)
    {
        parent::__construct();
        $this->tenantService = $tenantService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $planSlug = $this->option('plan');
        $password = $this->option('password') ?? Str::random(12);

        try {
            // Check if plan exists
            $plan = SubscriptionPlan::where('slug', $planSlug)->first();
            if (!$plan) {
                $this->error("Subscription plan '{$planSlug}' not found");
                return 1;
            }

            // Generate slug, tenant_id and database name
            $slug = Merchant::generateSlug($name);
            $tenantId = Merchant::generateTenantId();
            $databaseName = Merchant::generateDatabaseName($tenantId);

            $this->info("Creating tenant: {$name}");
            $this->info("Slug: {$slug}");
            $this->info("Tenant ID: {$tenantId}");
            $this->info("Database: {$databaseName}");

            // Create merchant
            $merchant = Merchant::create([
                'name' => $name,
                'slug' => $slug,
                'tenant_id' => $tenantId,
                'database_name' => $databaseName,
                'email' => $email,
                'status' => true,
            ]);

            $this->info("✓ Merchant created");

            // Create tenant database and run migrations
            $this->tenantService->createTenant($merchant);
            $this->info("✓ Tenant database created and migrated");

            // Create subscription
            $subscription = MerchantSubscription::create([
                'merchant_id' => $merchant->id,
                'subscription_plan_id' => $plan->id,
                'status' => 'active',
                'starts_at' => now(),
                'ends_at' => now()->addDays(30),
                'trial_ends_at' => $plan->trial_days > 0 ? now()->addDays($plan->trial_days) : null,
            ]);

            $this->info("✓ Subscription created");

            // Create admin user
            $adminUser = \App\Models\Global\MerchantUser::create([
                'merchant_id' => $merchant->id,
                'name' => 'Admin',
                'email' => $email,
                'password' => bcrypt($password),
                'role' => 'super_admin',
                'is_active' => true,
            ]);

            $this->info("✓ Admin user created");

            $this->info("\n🎉 Tenant created successfully!");
            $this->table(
                ['Field', 'Value'],
                [
                    ['Merchant Name', $merchant->name],
                    ['Slug', $merchant->slug],
                    ['Tenant ID', $merchant->tenant_id],
                    ['Database', $merchant->database_name],
                    ['Admin Email', $adminUser->email],
                    ['Admin Password', $password],
                    ['Plan', $plan->name],
                    ['Status', $merchant->status ? 'Active' : 'Inactive'],
                ]
            );

            return 0;
        } catch (\Exception $e) {
            $this->error("Failed to create tenant: " . $e->getMessage());
            return 1;
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\TenantRegistrationRequest;
use App\Models\Global\Merchant;
use App\Models\Global\SubscriptionPlan;
use App\Models\Global\MerchantSubscription;
use App\Models\Global\MerchantUser;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TenantRegistrationController extends Controller
{
    protected TenantService $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Show the tenant registration form.
     */
    public function create()
    {
        $plans = SubscriptionPlan::where('status', true)
            ->orderBy('price')
            ->get();

        return view('tenant-registration', compact('plans'));
    }

    /**
     * Handle tenant registration.
     */
    public function store(TenantRegistrationRequest $request)
    {
        try {

            // Generate unique identifiers
            $slug = Merchant::generateSlug($request->company_name);
            $tenantId = Merchant::generateTenantId();
            $databaseName = Merchant::generateDatabaseName($tenantId);

            $plan = SubscriptionPlan::where('slug', '=', 'free')->first();

            // Create merchant
            $merchant = Merchant::create([
                'name' => $request->company_name,
                'slug' => $slug,
                'tenant_id' => $tenantId,
                'database_name' => $databaseName,
                'email' => $request->admin_email,
                'status' => true,
            ]);

            // Create tenant database and run migrations
            $this->tenantService->createTenant($merchant);

            // Create subscription
            $subscription = MerchantSubscription::create([
                'merchant_id' => $merchant->id,
                'subscription_plan_id' => $plan->id,
                'status' => 'active',
                'starts_at' => now(),
                'ends_at' => $plan->trial_days > 0 ? now()->addDays($plan->trial_days) : now()->addDays(0),
                'trial_ends_at' => $plan->trial_days > 0 ? now()->addDays($plan->trial_days) : null,
            ]);

            // Create admin user in global database (for merchant management)
            $adminUser = MerchantUser::create([
                'merchant_id' => $merchant->id,
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => Hash::make($request->password),
                'role' => 'super_admin',
                'is_active' => true,
            ]);

            // Create admin user in tenant database (for actual application login)
            // Set connection to tenant database
            $this->tenantService->setTenantConnection($merchant);
            
            // Create user in tenant database
            $tenantUser = \App\Models\User::create([
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => Hash::make($request->password),
                'email_verified_at' => now(), // Auto-verify admin user
            ]);

            // Reset to global connection
            $this->tenantService->resetToGlobalConnection();

            // Redirect to success page with tenant info
            return view('tenant-registration-success', [
                'merchant' => $merchant,
                'plan' => $plan,
                'adminEmail' => $request->admin_email,
                'tenantUrl' => url("/{$tenantId}"),
            ]);
        } catch (\Exception $e) {
            return back()->withErrors([
                'general' => 'Failed to create tenant: ' . $e->getMessage()
            ])->withInput();
        }
    }
}

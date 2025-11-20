<?php

namespace App\Http\Controllers\Global;

use App\Http\Controllers\Controller;
use App\Http\Requests\TenantRegistrationRequest;
use App\Models\Global\Merchant;
use App\Models\Global\SubscriptionPlan;
use App\Models\Global\MerchantSubscription;
use App\Models\Global\MerchantUser;
use App\Services\TenantService;
use App\Mail\TenantEmailVerification;
use App\Jobs\SendTenantEmailVerification;
use App\Jobs\SetupTenantDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

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
        DB::beginTransaction();

        try {
            // Generate unique identifiers
            $slug = Merchant::generateSlug($request->company_name);
            $tenantId = Merchant::generateTenantId();
            $databaseName = Merchant::generateDatabaseName($tenantId);

            // Get free trial plan
            $plan = SubscriptionPlan::where('slug', '=', 'free')->first();
            if (!$plan) {
                return back()->withErrors([
                    'general' => 'Default subscription plan not found. Please contact support.'
                ])->withInput();
            }

            $settings = [
                'currency' => 'IDR',
                'locale' => 'id_ID',
                'timezone' => 'Asia/Jakarta',
            ];

            // Create merchant (email not verified yet)
            $merchant = Merchant::create([
                'name' => $request->company_name,
                'slug' => $slug,
                'tenant_id' => $tenantId,
                'database_name' => $databaseName,
                'email' => $request->admin_email,
                'email_verified_at' => null,
                'status' => true,
                'settings' => $settings,
            ]);

            // Create subscription
            $subscription = MerchantSubscription::create([
                'merchant_id' => $merchant->id,
                'subscription_plan_id' => $plan->id,
                'status' => 'active',
                'starts_at' => now(),
                'ends_at' => $plan->trial_days > 0 ? now()->addDays($plan->trial_days) : now()->addDays(0),
                'trial_ends_at' => $plan->trial_days > 0 ? now()->addDays($plan->trial_days) : null,
            ]);

            // Create admin user in global database
            $adminUser = MerchantUser::create([
                'merchant_id' => $merchant->id,
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => Hash::make($request->password),
                'role' => 'super_admin',
                'is_active' => true,
            ]);

            // Dispatch tenant setup job to background (migrations & seeding)
            SetupTenantDatabase::dispatch($merchant);

            // Dispatch email verification job to tenant queue
            SendTenantEmailVerification::dispatch($merchant);

            DB::commit();

            // Redirect to success page with tenant info
            return view('tenant-registration-success', [
                'merchant' => $merchant,
                'plan' => $plan,
                'adminEmail' => $request->admin_email,
                'tenantUrl' => url("/{$tenantId}"),
                'setupStatus' => 'processing',
                'note' => 'Your tenant database is being configured. You can try logging in after a few moments.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'general' => 'Failed to create tenant: ' . $e->getMessage()
            ])->withInput();
        }
    }
}

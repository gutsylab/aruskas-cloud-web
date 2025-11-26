<?php

namespace App\Http\Controllers\Api\Global;

use App\Models\Global\Merchant;
use App\Services\TenantService;
use Illuminate\Support\Facades\DB;
use App\Models\Global\MerchantUser;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Jobs\SetupTenantDatabase;
use App\Models\Global\SubscriptionPlan;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\ApiController;
use App\Models\Global\MerchantSubscription;
use App\Http\Requests\TenantRegistrationRequest;

class RegistrationController extends ApiController
{
    protected TenantService $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    public function tenantInfo()
    {
        $validated = request()->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);
        $email = $validated['email'];
        $merchantUser = MerchantUser::where('email', '=', $email)->first();
        if ($merchantUser) {
            $merchant = Merchant::select(
                [
                    'name',
                    'slug',
                    'tenant_id',
                    'email',
                    'logo',
                    'address',
                    'phone',
                    'website',
                    'status',
                ]
            )->find($merchantUser->merchant_id);
            if ($merchant) {
                return $this->responseSuccess([
                    'tenant' => $merchant
                ]);
            }
        }
        return null;
    }

<<<<<<< HEAD
=======
    /**
     * Handle tenant registration via API.
     *
     * @group Tenant Management
     * @bodyParam company_name string required The company/merchant name. Example: ABC Company
     * @bodyParam admin_name string required The administrator's full name. Example: John Doe
     * @bodyParam admin_email string required The administrator's email address. Example: admin@example.com
     * @bodyParam password string required The password (minimum 8 characters). Example: password123
     * @bodyParam password_confirmation string required Password confirmation. Example: password123
     * @bodyParam terms boolean required Accept terms and conditions. Example: true
     *
     * @response 201 {
     *   "success": true,
     *   "message": "Tenant registered successfully. Please check your email to verify your account.",
     *   "data": {
     *     "merchant": {
     *       "id": 1,
     *       "name": "ABC Company",
     *       "slug": "abc-company",
     *       "tenant_id": "TNT123456",
     *       "email": "admin@example.com",
     *       "status": true
     *     },
     *     "subscription": {
     *       "plan": "Free Trial",
     *       "status": "active",
     *       "trial_ends_at": "2025-12-03T00:00:00.000000Z"
     *     },
     *     "tenant_url": "http://localhost/{tenant_id}",
     *     "admin_email": "admin@example.com"
     *   }
     * }
     *
     * @response 422 {
     *   "success": false,
     *   "message": "Validation failed",
     *   "errors": {
     *     "admin_email": ["This email address is already registered."]
     *   }
     * }
     *
     * @response 500 {
     *   "success": false,
     *   "message": "Failed to create tenant: Error details"
     * }
     */
>>>>>>> origin/main
    public function register(TenantRegistrationRequest $request)
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
                return response()->json([
                    'success' => false,
<<<<<<< HEAD
                    'message' => 'Paket berlangganan gratis tidak ditemukan. Silakan hubungi tim support kami.'
=======
                    'message' => 'Default subscription plan not found. Please contact support.'
>>>>>>> origin/main
                ], 500);
            }

            $setings = [
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
                'settings' => $setings,
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
            SetupTenantDatabase::dispatch(
                $merchant,
                $request->admin_name,
                $request->admin_email,
                Hash::make($request->password)
            );

            DB::commit();

            return response()->json([
                'success' => true,
<<<<<<< HEAD
                'message' => 'Akun telah terdaftar dengan sukses. Akun Anda sedang disiapkan. Silakan periksa email Anda untuk memverifikasi akun.',
=======
                'message' => 'Tenant registered successfully. Your account is being set up. Please check your email to verify your account.',
>>>>>>> origin/main
                'data' => [
                    'merchant' => [
                        'id' => $merchant->id,
                        'name' => $merchant->name,
                        'slug' => $merchant->slug,
                        'tenant_id' => $merchant->tenant_id,
                        'email' => $merchant->email,
                        'status' => $merchant->status,
                    ],
                    'subscription' => [
                        'plan' => $plan->name,
                        'status' => $subscription->status,
                        'trial_ends_at' => $subscription->trial_ends_at,
                    ],
                    'tenant_url' => url("/{$tenantId}"),
                    'admin_email' => $request->admin_email,
                    'setup_status' => 'processing',
<<<<<<< HEAD
                    'note' => 'Akun Anda sedang disiapkan. Silakan periksa email Anda untuk memverifikasi akun.'
=======
                    'note' => 'Your tenant database is being configured. You can try logging in after a few moments.'
>>>>>>> origin/main
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
<<<<<<< HEAD
                'message' => 'Gagal membuat tenant: ' . $e->getMessage()
=======
                'message' => 'Failed to create tenant: ' . $e->getMessage()
>>>>>>> origin/main
            ], 500);
        }
    }

<<<<<<< HEAD
=======
    /**
     * Get available subscription plans.
     *
     * @group Tenant Management
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "plans": [
     *       {
     *         "id": 1,
     *         "name": "Free Trial",
     *         "slug": "free-trial",
     *         "price": 0,
     *         "trial_days": 30,
     *         "features": "Basic features"
     *       }
     *     ]
     *   }
     * }
     */
>>>>>>> origin/main
    public function getPlans()
    {
        $plans = SubscriptionPlan::where('status', true)
            ->orderBy('price')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'plans' => $plans
            ]
        ]);
    }
}

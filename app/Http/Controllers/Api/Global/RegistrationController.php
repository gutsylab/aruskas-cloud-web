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
                    'message' => 'Paket berlangganan gratis tidak ditemukan. Silakan hubungi tim support kami.'
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
                'message' => 'Akun telah terdaftar dengan sukses. Akun Anda sedang disiapkan. Silakan periksa email Anda untuk memverifikasi akun.',
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
                    'note' => 'Akun Anda sedang disiapkan. Silakan periksa email Anda untuk memverifikasi akun.'
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat tenant: ' . $e->getMessage()
            ], 500);
        }
    }

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

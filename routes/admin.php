<?php

use App\Models\Global\Merchant;
use App\Models\Global\SubscriptionPlan;
use App\Models\Global\MerchantSubscription;
use App\Models\Global\MerchantUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Global Admin Routes
|--------------------------------------------------------------------------
|
| These routes are for global administration and use the global database.
| They are not tenant-specific and should only be accessible by 
| super administrators.
|
*/

Route::prefix('admin')->group(function () {
    
    // Dashboard
    Route::get('/', function () {
        $stats = [
            'total_merchants' => Merchant::count(),
            'active_merchants' => Merchant::where('status', true)->count(),
            'total_users' => MerchantUser::count(),
            'total_subscriptions' => MerchantSubscription::where('status', 'active')->count(),
        ];
        
        return response()->json($stats);
    });

    // Merchants management
    Route::prefix('merchants')->group(function () {
        Route::get('/', function () {
            return Merchant::with(['activeSubscription.plan', 'users'])
                ->paginate(15);
        });

        Route::get('/{merchant}', function (Merchant $merchant) {
            return $merchant->load(['activeSubscription.plan', 'users', 'subscriptions']);
        });

        Route::post('/', function (Request $request) {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:merchant_users,email',
                'plan_id' => 'required|exists:subscription_plans,id',
            ]);

            $slug = Merchant::generateSlug($validated['name']);
            $databaseName = Merchant::generateDatabaseName($slug);

            $merchant = Merchant::create([
                'name' => $validated['name'],
                'slug' => $slug,
                'database_name' => $databaseName,
                'email' => $validated['email'],
                'status' => true,
            ]);

            // Create subscription
            $plan = SubscriptionPlan::find($validated['plan_id']);
            MerchantSubscription::create([
                'merchant_id' => $merchant->id,
                'subscription_plan_id' => $plan->id,
                'status' => 'active',
                'starts_at' => now(),
                'ends_at' => now()->addDays(30),
                'trial_ends_at' => $plan->trial_days > 0 ? now()->addDays($plan->trial_days) : null,
            ]);

            // Create admin user
            MerchantUser::create([
                'merchant_id' => $merchant->id,
                'name' => 'Admin',
                'email' => $validated['email'],
                'password' => bcrypt('password123'),
                'role' => 'super_admin',
                'is_active' => true,
            ]);

            return response()->json($merchant->load(['activeSubscription.plan']), 201);
        });

        Route::put('/{merchant}', function (Request $request, Merchant $merchant) {
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email',
                'status' => 'sometimes|boolean',
            ]);

            $merchant->update($validated);
            return response()->json($merchant);
        });

        Route::delete('/{merchant}', function (Merchant $merchant) {
            // This should also handle dropping the tenant database
            $merchant->delete();
            return response()->json(['message' => 'Merchant deleted successfully']);
        });
    });

    // Subscription Plans management
    Route::prefix('plans')->group(function () {
        Route::get('/', function () {
            return SubscriptionPlan::paginate(15);
        });

        Route::post('/', function (Request $request) {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'required|string|unique:subscription_plans,slug',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'billing_cycle' => 'required|in:monthly,yearly,lifetime',
                'trial_days' => 'required|integer|min:0',
                'features' => 'required|array',
                'limits' => 'required|array',
            ]);

            $plan = SubscriptionPlan::create($validated);
            return response()->json($plan, 201);
        });

        Route::put('/{plan}', function (Request $request, SubscriptionPlan $plan) {
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'sometimes|nullable|string',
                'price' => 'sometimes|numeric|min:0',
                'billing_cycle' => 'sometimes|in:monthly,yearly,lifetime',
                'trial_days' => 'sometimes|integer|min:0',
                'features' => 'sometimes|array',
                'limits' => 'sometimes|array',
                'status' => 'sometimes|boolean',
            ]);

            $plan->update($validated);
            return response()->json($plan);
        });

        Route::delete('/{plan}', function (SubscriptionPlan $plan) {
            $plan->delete();
            return response()->json(['message' => 'Plan deleted successfully']);
        });
    });

    // Subscriptions management
    Route::prefix('subscriptions')->group(function () {
        Route::get('/', function () {
            return MerchantSubscription::with(['merchant', 'plan'])
                ->paginate(15);
        });

        Route::put('/{subscription}', function (Request $request, MerchantSubscription $subscription) {
            $validated = $request->validate([
                'status' => 'sometimes|in:active,inactive,canceled,expired',
                'ends_at' => 'sometimes|date',
            ]);

            $subscription->update($validated);
            return response()->json($subscription->load(['merchant', 'plan']));
        });
    });
});

// API routes for global operations
Route::prefix('api/global')->group(function () {
    Route::get('/plans', function () {
        return SubscriptionPlan::where('status', true)->get();
    });
    
    Route::post('/register-merchant', function (Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:merchant_users,email',
            'password' => 'required|string|min:8',
            'plan_slug' => 'required|exists:subscription_plans,slug',
        ]);

        $plan = SubscriptionPlan::where('slug', $validated['plan_slug'])->first();
        
        $slug = Merchant::generateSlug($validated['name']);
        $databaseName = Merchant::generateDatabaseName($slug);

        $merchant = Merchant::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'database_name' => $databaseName,
            'email' => $validated['email'],
            'status' => true,
        ]);

        // Create subscription
        MerchantSubscription::create([
            'merchant_id' => $merchant->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addDays(30),
            'trial_ends_at' => $plan->trial_days > 0 ? now()->addDays($plan->trial_days) : null,
        ]);

        // Create admin user
        MerchantUser::create([
            'merchant_id' => $merchant->id,
            'name' => 'Admin',
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Merchant registered successfully',
            'merchant' => $merchant,
            'login_url' => "http://{$slug}." . request()->getHost(),
        ], 201);
    });
});

<?php

use App\Http\Middleware\ApiKeyAuth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Tenant\AuthController;
use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Api\Global\RegistrationController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\Tenant\Report\AccountingController;
use App\Http\Controllers\Api\Tenant\AccountController as ApiAccountController;
use App\Http\Controllers\Api\Tenant\CashFlowController as ApiCashFlowController;
use App\Http\Controllers\Api\Tenant\CashTransferController as ApiCashTransferController;

// Global API routes (no tenant middleware required)
// Laravel automatically adds 'api/' prefix, so we only need 'v1'
// Convert JSON response keys to camelCase for all API responses in this group
Route::prefix('v1')->middleware(\App\Http\Middleware\ConvertResponseKeysToCamelCase::class)->group(function () {
    // Tenant Registration API
    Route::prefix('tenant')->group(function () {
        Route::get('/plans', [RegistrationController::class, 'getPlans']);
        Route::post('/register', [RegistrationController::class, 'register']);
        Route::post('/info', [RegistrationController::class, 'tenantInfo']);
    });
});

// Tenant-specific API routes (with tenant middleware)
// Pattern: /api/{tenant_id}/v1/... (Laravel adds 'api/' prefix automatically)
// Add response key conversion middleware in addition to tenant resolver
Route::prefix('{tenant_id}/v1')->middleware(['tenant', \App\Http\Middleware\ConvertResponseKeysToCamelCase::class])->group(function () {

    // Tenant Auth API endpoints (no CSRF required for API)
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);

        // Protected auth routes
        Route::middleware('tenant.sanctum')->group(function () {
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/logout-all', [AuthController::class, 'logoutAll']);
            Route::post('/refresh', [AuthController::class, 'refresh']);
        });
    });

    // Legacy Auth API endpoints (backward compatibility)
    Route::prefix('auth')->group(function () {
        Route::post('/register', [RegisteredUserController::class, 'store']);
        Route::post('/login-legacy', [AuthenticatedSessionController::class, 'store']);
        Route::post('/logout-legacy', [AuthenticatedSessionController::class, 'destroy'])->middleware('tenant.sanctum');
    });

    // Protected API routes (require authentication)
    Route::middleware('tenant.sanctum')->group(function () {
        Route::get('/user', function () {
            $tenant = request()->attributes->get('tenant');
            return response()->json([
                'user' => auth()->user(),
                'tenant' => $tenant,
            ]);
        });

        Route::prefix('account')->group(function () {
            Route::get('/', [ApiAccountController::class, 'index']);
            Route::get('/{id}', [ApiAccountController::class, 'show']);
            Route::post('/', [ApiAccountController::class, 'store']);
            Route::put('/{id}', [ApiAccountController::class, 'update']);
            Route::delete('/{id}', [ApiAccountController::class, 'destroy']);
        });

        Route::prefix('cash-flow')->group(function () {
            Route::get('/show/{id}', [ApiCashFlowController::class, 'show']);
            Route::get('/{type}', [ApiCashFlowController::class, 'index'])->defaults('type', 'in');

            Route::post('/', [ApiCashFlowController::class, 'store']);
            Route::put('/{id}', [ApiCashFlowController::class, 'update']);
            Route::delete('/{id}', [ApiCashFlowController::class, 'destroy']);
            Route::patch('/{id}/set-draft', [ApiCashFlowController::class, 'set_draft']);
            Route::patch('/{id}/set-posted', [ApiCashFlowController::class, 'set_posted']);
            Route::delete('/{id}', [ApiCashFlowController::class, 'destroy']);
        });

        Route::prefix('cash-transfer')->group(function () {
            Route::get('/', [ApiCashTransferController::class, 'index']);
            Route::get('/show/{id}', [ApiCashTransferController::class, 'show']);

            Route::post('/', [ApiCashTransferController::class, 'store']);
            Route::put('/{id}', [ApiCashTransferController::class, 'update']);
            Route::delete('/{id}', [ApiCashTransferController::class, 'destroy']);
            Route::patch('/{id}/set-draft', [ApiCashTransferController::class, 'set_draft']);
            Route::patch('/{id}/set-posted', [ApiCashTransferController::class, 'set_posted']);
            Route::delete('/{id}', [ApiCashTransferController::class, 'destroy']);
        });

        Route::prefix('report')->group(function () {
            Route::prefix('accounting')->group(function () {
                Route::get('/profit-loss', [AccountingController::class, 'report_profit_loss']);
                Route::get('/bank-statement', [AccountingController::class, 'report_bank_statement']);
                Route::get('/cash-flow-summary', [AccountingController::class, 'report_cash_flow_summary']);
                Route::get('/cash-flow-detail', [AccountingController::class, 'report_cash_flow_detail']);
            });
        });

        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::get('/profile', [DashboardController::class, 'profile']);
        Route::put('/profile', [DashboardController::class, 'updateProfile']);
    });
});

// Legacy email API routes - Temporarily disabled
// Route::middleware([
//     ApiKeyAuth::class,
//     'throttle:client'
// ])->group(function () {
//     Route::post('/send', [EmailSendController::class, 'send']);
//     Route::post('/send-now', [EmailSendController::class, 'sendNow']);
//     Route::get('/messages/{id}', [EmailSendController::class, 'show']);
// });

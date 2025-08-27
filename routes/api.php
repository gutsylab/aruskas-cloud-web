<?php

use App\Http\Middleware\ApiKeyAuth;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Tenant\EmailSendController; // Temporarily disabled
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Tenant\DashboardController;

// Tenant-specific API routes (with tenant middleware)
Route::middleware(['tenant'])->group(function () {
    
    // Auth API endpoints (no CSRF required for API)
    Route::prefix('auth')->group(function () {
        Route::post('/register', [RegisteredUserController::class, 'store']);
        Route::post('/login', [AuthenticatedSessionController::class, 'store']);
        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth:sanctum');
    });

    // Protected API routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', function () {
            $tenant = request()->attributes->get('tenant');
            return response()->json([
                'user' => auth()->user(),
                'tenant' => $tenant,
            ]);
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

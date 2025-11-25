<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Global\EmailVerificationController;
use App\Http\Controllers\Global\TenantRegistrationController;
use App\Http\Controllers\Tenant\CashAccountController;
use App\Http\Controllers\Tenant\CashCategoryController;
use App\Http\Controllers\Tenant\CashFlowController;
use App\Http\Controllers\Tenant\DashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Global routes (no tenant required)
Route::get('/', function () {
    return view('welcome');
});

Route::get('/register', [TenantRegistrationController::class, 'create'])->name('tenant.register');
Route::post('/register', [TenantRegistrationController::class, 'store'])->name('tenant.register.store');

// Email verification routes
Route::get('/verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->name('tenant.email.verify')
    ->middleware(['signed']);
Route::get('/resend-verification', [EmailVerificationController::class, 'showResendForm'])
    ->name('tenant.email.resend.form');
Route::post('/resend-verification', [EmailVerificationController::class, 'resend'])
    ->name('tenant.email.resend');

// Tenant-specific routes with tenant ID in path: /{tenant_id}/...
Route::prefix('{tenant_id}')->middleware(['tenant'])->group(function () {

    // get first segment of the url
    $firstSegment = request()->segment(1);

    // Root tenant route - redirect to dashboard if authenticated
    Route::get('/', function () {
        $tenantId = request()->route('tenant_id');

        if (! $tenantId) {
            abort(404, 'Tenant not found');
        }

        if (Auth::check()) {
            return redirect()->route('dashboard', ['tenant_id' => $tenantId]);
        }
        return redirect()->route('login', ['tenant_id' => $tenantId]);
    })->name('tenant.home');

    // Guest routes (not authenticated)
    Route::middleware('guest')->group(function () {
        Route::get('/register', [RegisteredUserController::class, 'create'])->name('tenant.user.register');
        Route::post('/register', [RegisteredUserController::class, 'store']);

        Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    });

    // Main dashboard route with tenant-aware auth
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('tenant.auth')
        ->name('dashboard');

    // Authenticated routes using tenant-aware auth
    Route::middleware('tenant.auth')->group(function () {
        Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');

        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

        Route::get('/cash-flows/dt', [CashFlowController::class, 'dataTable'])->name('cash-flows.dt');
        Route::resource('/cash-flows', CashFlowController::class);

        Route::get('/cash-categories/dt', [CashCategoryController::class, 'dataTable'])->name('cash-categories.dt');
        Route::resource('/cash-categories', CashCategoryController::class);

        Route::get('/cash-accounts/dt', [CashAccountController::class, 'dataTable'])->name('cash-accounts.dt');
        Route::resource('/cash-accounts', CashAccountController::class);

        // Legacy route for backward compatibility
        Route::get('/users', function () {
            return \App\Models\Tenant\User::all();
        });

        // Debug route - remove in production
        Route::get('/debug-auth', function () {
            $tenant = request()->attributes->get('tenant');
            $user   = Auth::user();

            return response()->json([
                'authenticated'     => Auth::check(),
                'user_id'           => $user ? $user->id : null,
                'user_email'        => $user ? $user->email : null,
                'tenant_name'       => $tenant ? $tenant->name : null,
                'tenant_id'         => $tenant ? $tenant->tenant_id : null,
                'tenant_path_param' => request()->route('tenant_id'),
                'session_id'        => session()->getId(),
                'session_data'      => [
                    'tenant_id'         => session('tenant_id'),
                    'tenant_connection' => session('tenant_connection'),
                ],
            ]);
        });
    });

    // Test route for debugging
    Route::get('/test', function () {
        $tenant = request()->attributes->get('tenant');

        return response()->json([
            'status'            => 'tenant_test_accessed',
            'tenant_path_param' => request()->route('tenant_id'),
            'tenant_resolved'   => $tenant ? $tenant->tenant_id : null,
            'tenant_name'       => $tenant ? $tenant->name : null,
        ]);
    })->name('tenant.test');
});

// Admin routes dengan sidebar
// Route::get('/admin/dashboard', function () {
//     return view('admin.dashboard');
// })->name('admin.dashboard');

// AJAX Demo page
// Route::get('/admin/ajax-demo', function () {
//     return view('admin.ajax-demo');
// })->name('admin.ajax-demo');

// UI Components Demo page
// Route::get('/admin/ui-components', function () {
//     return view('admin.ui-components');
// })->name('admin.ui-components');

// File Manager page
// Route::get('/admin/file-manager', function () {
//     return view('admin.file-manager');
// })->name('admin.file-manager');

// File Manager API routes
// Route::prefix('admin/file-manager')->name('admin.file-manager.')->group(function () {
//     Route::get('/api/files', [FileManagerController::class, 'getFiles'])->name('api.files');
//     Route::post('/api/upload', [FileManagerController::class, 'uploadFile'])->name('api.upload');
//     Route::post('/api/create-folder', [FileManagerController::class, 'createFolder'])->name('api.create-folder');
//     Route::delete('/api/delete', [FileManagerController::class, 'deleteItem'])->name('api.delete');
//     Route::get('/api/download', [FileManagerController::class, 'downloadFile'])->name('api.download');
// });

// Contoh route admin lainnya
// Route::prefix('admin')->name('admin.')->group(function () {
//     Route::get('/users', function () {
//         return view('admin.users');
//     })->name('users');

//     Route::get('/products', function () {
//         return view('admin.dashboard'); // Sementara gunakan dashboard view
//     })->name('products');

//     Route::get('/orders', function () {
//         return view('admin.dashboard'); // Sementara gunakan dashboard view
//     })->name('orders');

//     Route::get('/reports', function () {
//         return view('admin.dashboard'); // Sementara gunakan dashboard view
//     })->name('reports');

//     Route::get('/settings', function () {
//         return view('admin.dashboard'); // Sementara gunakan dashboard view
//     })->name('settings');
// });

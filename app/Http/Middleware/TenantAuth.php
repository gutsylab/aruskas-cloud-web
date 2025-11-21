<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\TenantService;
use App\Models\Tenant\User;

class TenantAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Get tenant from request (should be set by TenantResolver middleware)
        $tenant = $request->attributes->get('tenant');

        if (!$tenant) {
            return redirect('/');
        }

        // Check if user is authenticated in session
        if (!Auth::check()) {
            // Try to restore authentication from session using tenant database
            $userId = session('auth.user_id');
            if ($userId) {
                $tenantService = app(TenantService::class);
                $connectionName = $tenantService->getTenantConnectionName($tenant);

                // Get user from tenant database
                $user = User::on($connectionName)->find($userId);
                if ($user) {
                    // Restore authentication
                    Auth::login($user);
                } else {
                    // Clear invalid session
                    session()->forget('auth.user_id');
                    return redirect()->route('login', ['tenant_id' => $tenant->tenant_id]);
                }
            } else {
                return redirect()->route('login', ['tenant_id' => $tenant->tenant_id]);
            }
        }

        return $next($request);
    }
}

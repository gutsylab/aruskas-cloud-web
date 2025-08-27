<?php

namespace App\Http\Middleware;

use App\Services\TenantService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantResolver
{
    protected TenantService $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Try to resolve tenant from path
        $tenant = $this->tenantService->getCurrentTenant();

        if (!$tenant) {
            return $this->handleMissingTenant($request);
        }

        if (!$tenant->isActive()) {
            return $this->handleInactiveTenant($request);
        }

        // Set tenant connection
        $this->tenantService->setTenantConnection($tenant);
        
        // Store tenant connection info in session for auth
        session([
            'tenant_id' => $tenant->tenant_id,
            'tenant_connection' => "tenant_{$tenant->tenant_id}",
        ]);
        
        // Make tenant available in the request
        $request->attributes->set('tenant', $tenant);
        
        return $next($request);
    }

    /**
     * Handle missing tenant.
     */
    private function handleMissingTenant(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Tenant not found',
                'message' => 'The requested tenant could not be found.'
            ], 404);
        }

        abort(404, 'Tenant not found');
    }

    /**
     * Handle inactive tenant.
     */
    private function handleInactiveTenant(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Tenant inactive',
                'message' => 'This tenant is currently inactive.'
            ], 403);
        }

        abort(403, 'Tenant is inactive');
    }
}

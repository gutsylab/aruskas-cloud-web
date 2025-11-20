<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class SanctumTenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $request->attributes->get('tenant');

        if (!$tenant) {
            return response()->json([
                'message' => 'Tenant not found'
            ], 404);
        }

        // Get token from request
        $bearerToken = $request->bearerToken();

        if (!$bearerToken) {
            return response()->json([
                'message' => 'Unauthenticated.'
            ], 401);
        }

        // Extract the actual token (remove ID prefix if exists for backward compatibility)
        // New tokens: plaintext only
        // Old tokens: {id}|{plaintext_token}
        $tokenParts = explode('|', $bearerToken, 2);
        $token = count($tokenParts) === 2 ? $tokenParts[1] : $bearerToken;

        // Set tenant connection
        $connectionName = "tenant_{$tenant->tenant_id}";

        // Hash the token to match database storage
        $hashedToken = hash('sha256', $token);

        // Find token in tenant database
        $accessToken = PersonalAccessToken::on($connectionName)
            ->where('token', $hashedToken)
            ->first();

        if (!$accessToken || !$accessToken->tokenable) {
            return response()->json([
                'message' => 'Unauthenticated.',
                'debug' => config('app.debug') ? [
                    'token_provided' => substr($bearerToken, 0, 20) . '...',
                    'connection' => $connectionName,
                    'token_found' => $accessToken ? 'yes' : 'no',
                ] : null,
            ], 401);
        }

        // Check if token is expired
        if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
            return response()->json([
                'message' => 'Token expired.'
            ], 401);
        }

        // Set the user on the connection
        $user = $accessToken->tokenable;
        $user->setConnection($connectionName);

        // Set the current access token on the user (important for currentAccessToken() to work)
        $user->withAccessToken($accessToken);

        // Set the authenticated user
        Auth::setUser($user);
        $request->setUserResolver(fn() => $user);

        // Update last used timestamp
        $accessToken->forceFill(['last_used_at' => now()])->save();

        return $next($request);
    }
}

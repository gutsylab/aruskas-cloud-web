<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpFoundation\Response;

class HandleTokenMismatch
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            return $next($request);
        } catch (TokenMismatchException $e) {
            // Extract tenant ID from URL path
            $path = $request->path();
            preg_match('/^([A-Z0-9]+)\//', $path, $matches);

            if (!empty($matches[1])) {
                $tenantId = $matches[1];
                // Redirect to tenant login with error message
                return redirect("/{$tenantId}/login")
                    ->withErrors(['error' => 'Halaman telah kadaluarsa. Silakan lakukan login lagi.'])
                    ->withInput($request->except('_token', 'password'));
            }

            // If no tenant context, redirect to home
            return redirect('/')
                ->withErrors(['error' => 'Halaman telah kadaluarsa. Silakan coba lagi.']);
        }
    }
}

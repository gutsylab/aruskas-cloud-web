<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Session\TokenMismatchException;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws \Illuminate\Session\TokenMismatchException
     */
    public function handle($request, \Closure $next)
    {
        try {
            return parent::handle($request, $next);
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

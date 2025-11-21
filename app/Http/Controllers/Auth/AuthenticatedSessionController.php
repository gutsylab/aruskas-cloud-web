<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create()
    {
        $tenant = request()->attributes->get('tenant');

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        return view('auth.login', compact('tenant'));
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request)
    {
        $tenant = request()->attributes->get('tenant');

        if (!$tenant) {
            return response()->json(['error' => 'Tenant not found'], 404);
        }

        // Validate input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Manual authentication with tenant-specific user lookup
        $connectionName = "tenant_{$tenant->tenant_id}";

        // Find user in tenant database
        $user = \App\Models\Tenant\User::on($connectionName)
            ->where('email', $request->email)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'email' => 'These credentials do not match our records.',
            ])->onlyInput('email');
        }

        // IMPORTANT: Set the connection before login
        $user->setConnection($connectionName);

        // Log the user in with custom session data
        Auth::login($user, $request->boolean('remember'));

        $request->session()->regenerate();

        // Store additional debug info
        session([
            'debug_user_id' => $user->id,
            'debug_user_email' => $user->email,
            'debug_tenant_id' => $tenant->tenant_id,
            'debug_connection' => $connectionName,
        ]);

        // Debug after login
        error_log("=== LOGIN SUCCESS DEBUG ===");
        error_log("User logged in: " . $user->email);
        error_log("Auth check: " . (Auth::check() ? 'YES' : 'NO'));
        error_log("Session ID: " . session()->getId());
        error_log("Intended URL: " . $request->session()->get('url.intended', 'none'));
        error_log("===========================");

        if ($request->expectsJson()) {
            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
                'tenant' => $tenant,
                'token' => $token,
            ]);
        }

        return redirect()->route('dashboard', ['tenant_id' => $tenant->tenant_id])
            ->with('success', 'Login successful!');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        $tenant = request()->attributes->get('tenant');

        if ($request->expectsJson()) {
            // For API requests, revoke the current token
            $request->user()->currentAccessToken()->delete();

            return response()->json(['message' => 'Logout successful']);
        }

        // For web requests
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // Redirect to tenant-specific login or home
        if ($tenant) {
            return redirect()->route('login', ['tenant_id' => $tenant->tenant_id])
                ->with('success', 'Logout successful!');
        }

        return redirect('/')->with('success', 'Logout successful!');
    }
}

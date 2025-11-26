<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Global\Merchant;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Auth\LoginRequest;
use App\Jobs\SendTenantEmailVerification;

class AuthenticatedSessionController extends BaseController
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

        // return view('auth.login', compact('tenant'));
        return $this->viewTenantAuth(compact('tenant'));
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
            ->with('success', 'Selamat datang kembali, ' . $user->name . '!');
    }

    public function resend_verification_email(Request $request)
    {
        $tenant = request()->attributes->get('tenant');

        if (!$tenant) {
            return response()->json(['error' => 'Tenant not found'], 404);
        }

        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            return back()->with('info', 'Email sudah terverifikasi.');
        }

        // Get merchant from global database
        $merchant = Merchant::where('tenant_id', $tenant->tenant_id)->first();

        if (!$merchant) {
            return back()->with('error', 'Data merchant tidak ditemukan.');
        }

        // Dispatch email verification job
        SendTenantEmailVerification::dispatch($merchant);

        return back()->with('success', 'Tautan verifikasi email telah dikirim ulang ke alamat email Anda.');
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

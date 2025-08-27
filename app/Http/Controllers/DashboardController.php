<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the tenant dashboard.
     */
    public function index(Request $request)
    {
        $tenant = request()->attributes->get('tenant');
        $user = Auth::user();
        
        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => "Welcome to {$tenant->name} dashboard",
                'tenant' => $tenant,
                'user' => $user,
                'stats' => [
                    'total_users' => \App\Models\User::on("tenant_{$tenant->tenant_id}")->count(),
                    'active_users' => \App\Models\User::on("tenant_{$tenant->tenant_id}")->whereNotNull('email_verified_at')->count(),
                ]
            ]);
        }

        return view('dashboard', compact('tenant', 'user'));
    }

    /**
     * Display user profile.
     */
    public function profile(Request $request)
    {
        $tenant = request()->attributes->get('tenant');
        $user = Auth::user();

        if ($request->expectsJson()) {
            return response()->json([
                'user' => $user,
                'tenant' => $tenant,
            ]);
        }

        return view('profile', compact('tenant', 'user'));
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Profile updated successfully',
                'user' => $user,
            ]);
        }

        return back()->with('success', 'Profile updated successfully!');
    }
}

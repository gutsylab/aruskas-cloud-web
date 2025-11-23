<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $tenant = request()->attributes->get('tenant');
        $user = Auth::user();

        return view('dashboard', [
            'tenant' => $tenant,
            'user' => $user
        ]);
    }

    public function profile()
    {
        $tenant = request()->attributes->get('tenant');
        $user = Auth::user();

        return view('profile', [
            'tenant' => $tenant,
            'user' => $user
        ]);
    }

    public function updateProfile(Request $request)
    {
        $tenant = request()->attributes->get('tenant');

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
        ]);

        $user = Auth::user();
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return back()->with('success', 'Profile updated successfully.');
    }
}

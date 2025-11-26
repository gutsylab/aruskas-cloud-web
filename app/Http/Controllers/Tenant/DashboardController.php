<?php

namespace App\Http\Controllers\Tenant;

use Illuminate\Http\Request;
use App\Constants\TenantPermissions;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BaseController;

class DashboardController extends BaseController
{

    private function groupMenu()
    {
        return [
            'application' => TenantPermissions::APPLICATION,
            'groupMenu' => TenantPermissions::GROUP_DASHBOARD,
            'subGroupMenu' => TenantPermissions::SUBGROUP_DASHBOARD,
            'moduleName' => TenantPermissions::MODULE_DASHBOARD,
        ];
    }
    public function index()
    {
        $tenant = request()->attributes->get('tenant');
        $user = Auth::user();

        return $this->viewTenant('dashboard', 'Dashboard', $this->groupMenu(), [
            'tenant' => $tenant,
            'user' => $user,
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

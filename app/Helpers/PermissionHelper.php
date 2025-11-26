<?php

use App\Models\Tenant\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;


if (!function_exists('userCan')) {
    function userCan($ability, $model = null)
    {
        return Gate::allows($ability, $model);
    }
}

if (!function_exists('hasPermissionModule')) {
    function hasPermissionModule($module)
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }
        $user = User::where('id', $user->id)->first();

        return $user->hasPermissionModule($module);
    }
}

if (!function_exists('hasPermissionGroup')) {
    function hasPermissionGroup($group)
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }
        $user = User::where('id', $user->id)->first();

        return $user->hasPermissionGroup($group);
    }
}

if (!function_exists('hasPermissionSubGroup')) {
    function hasPermissionSubGroup($sub_group)
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }
        $user = User::where('id', $user->id)->first();

        return $user->hasPermissionSubGroup($sub_group);
    }
}

if (!function_exists('hasPermissionApplication')) {
    function hasPermissionApplication($application)
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }
        $user = User::where('id', $user->id)->first();

        return $user->hasPermissionApplication($application);
    }
}

if (!function_exists('hasPermission')) {
    function hasPermission($permission)
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }
        $user = User::where('id', $user->id)->first();

        return $user->hasPermission($permission);
    }
}

<?php

namespace Database\Seeders\Tenant\Permissions;

use Illuminate\Database\Seeder;
use App\Models\Tenant\Permission;
use App\Constants\TenantPermissions;
use App\Models\Global\Merchant;
use App\Models\Tenant\User;
use App\Models\Tenant\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PermissionsUserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed data for tenant database

        Log::info("PermissionsUserRoleSeeder: Started");


        $group = TenantPermissions::GROUP_USER_AND_ROLE;
        $group_label = 'USER MANAGEMENT';
        $sub_group = TenantPermissions::SUBGROUP_USER;
        $sub_group_label = 'Pengguna';
        $module = TenantPermissions::MODULE_USER;
        $module_label = 'Pengguna';
        $application = TenantPermissions::APPLICATION;
        $application_label = 'Main Application';

        // User Permissions
        Permission::firstOrCreate([
            'name' => TenantPermissions::USER_VIEW_ALL,
        ], [
            'label' => 'Lihat Semua',
            'group' => $group,
            'group_label' => $group_label,
            'sub_group' => $sub_group,
            'sub_group_label' => $sub_group_label,
            'module' => $module,
            'module_label' => $module_label,
            'application' => $application,
            'application_label' => $application_label,
        ]);

        Permission::firstOrCreate([
            'name' => TenantPermissions::USER_VIEW,
        ], [
            'label' => 'Detail',
            'group' => $group,
            'group_label' => $group_label,
            'sub_group' => $sub_group,
            'sub_group_label' => $sub_group_label,
            'module' => $module,
            'module_label' => $module_label,
            'application' => $application,
            'application_label' => $application_label,
        ]);

        Permission::firstOrCreate([
            'name' => TenantPermissions::USER_CREATE,
        ], [
            'label' => 'Tambah',
            'group' => $group,
            'group_label' => $group_label,
            'sub_group' => $sub_group,
            'sub_group_label' => $sub_group_label,
            'module' => $module,
            'module_label' => $module_label,
            'application' => $application,
            'application_label' => $application_label,
        ]);

        Permission::firstOrCreate([
            'name' => TenantPermissions::USER_UPDATE,
        ], [
            'label' => 'Ubah',
            'group' => $group,
            'group_label' => $group_label,
            'sub_group' => $sub_group,
            'sub_group_label' => $sub_group_label,
            'module' => $module,
            'module_label' => $module_label,
            'application' => $application,
            'application_label' => $application_label,
        ]);

        Permission::firstOrCreate([
            'name' => TenantPermissions::USER_DELETE,
        ], [
            'label' => 'Hapus',
            'group' => $group,
            'group_label' => $group_label,
            'sub_group' => $sub_group,
            'sub_group_label' => $sub_group_label,
            'module' => $module,
            'module_label' => $module_label,
            'application' => $application,
            'application_label' => $application_label,
        ]);

        Permission::firstOrCreate([
            'name' => TenantPermissions::USER_RESET_PASSWORD,
        ], [
            'label' => 'Reset Password',
            'group' => $group,
            'group_label' => $group_label,
            'sub_group' => $sub_group,
            'sub_group_label' => $sub_group_label,
            'module' => $module,
            'module_label' => $module_label,
            'application' => $application,
            'application_label' => $application_label,
        ]);


        // Role Permissions
        $sub_group = TenantPermissions::SUBGROUP_ROLE;
        $sub_group_label = 'Akses';
        $module = TenantPermissions::MODULE_ROLE;
        $module_label = 'Akses';
        Permission::firstOrCreate([
            'name' => TenantPermissions::ROLE_VIEW_ALL,
        ], [
            'label' => 'Lihat Semua',
            'group' => $group,
            'group_label' => $group_label,
            'sub_group' => $sub_group,
            'sub_group_label' => $sub_group_label,
            'module' => $module,
            'module_label' => $module_label,
            'application' => $application,
            'application_label' => $application_label,
        ]);
        Permission::firstOrCreate([
            'name' => TenantPermissions::ROLE_VIEW,
        ], [
            'label' => 'Detail',
            'group' => $group,
            'group_label' => $group_label,
            'sub_group' => $sub_group,
            'sub_group_label' => $sub_group_label,
            'module' => $module,
            'module_label' => $module_label,
            'application' => $application,
            'application_label' => $application_label,
        ]);
        Permission::firstOrCreate([
            'name' => TenantPermissions::ROLE_CREATE,
        ], [
            'label' => 'Tambah',
            'group' => $group,
            'group_label' => $group_label,
            'sub_group' => $sub_group,
            'sub_group_label' => $sub_group_label,
            'module' => $module,
            'module_label' => $module_label,
            'application' => $application,
            'application_label' => $application_label,
        ]);
        Permission::firstOrCreate([
            'name' => TenantPermissions::ROLE_UPDATE,
        ], [
            'label' => 'Ubah',
            'group' => $group,
            'group_label' => $group_label,
            'sub_group' => $sub_group,
            'sub_group_label' => $sub_group_label,
            'module' => $module,
            'module_label' => $module_label,
            'application' => $application,
            'application_label' => $application_label,
        ]);
        Permission::firstOrCreate([
            'name' => TenantPermissions::ROLE_DELETE,
        ], [
            'label' => 'Hapus',
            'group' => $group,
            'group_label' => $group_label,
            'sub_group' => $sub_group,
            'sub_group_label' => $sub_group_label,
            'module' => $module,
            'module_label' => $module_label,
            'application' => $application,
            'application_label' => $application_label,
        ]);

        Log::info("PermissionsUserRoleSeeder: Completed");
    }
}

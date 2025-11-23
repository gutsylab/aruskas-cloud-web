<?php

namespace Database\Seeders\Tenant\Permissions;

use Illuminate\Database\Seeder;
use App\Models\Tenant\Permission;
use Illuminate\Support\Facades\Log;
use App\Constants\TenantPermissions;

class PermissionsCashSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Log::info("PermissionsCashSeeder: Started");
        // Seed data for tenant database

        $group = TenantPermissions::GROUP_CASH;
        $group_label = 'Kas';
        $sub_group = TenantPermissions::SUBGROUP_CASH_FLOW;
        $sub_group_label = 'Arus Kas';
        $module = TenantPermissions::MODULE_CASH_FLOW;
        $module_label = 'Arus Kas';
        $application = TenantPermissions::APPLICATION;
        $application_label = 'Main Application';

        // Cash Flow Permissions
        Permission::firstOrCreate([
            'name' => TenantPermissions::CASH_FLOW_VIEW_ALL,
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
            'name' => TenantPermissions::CASH_FLOW_VIEW,
        ], [
            'label' => 'Lihat Detail',
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
            'name' => TenantPermissions::CASH_FLOW_CREATE,
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
            'name' => TenantPermissions::CASH_FLOW_UPDATE,
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
            'name' => TenantPermissions::CASH_FLOW_DELETE,
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



        // Cash Category Permissions
        $sub_group = TenantPermissions::SUBGROUP_CASH_CATEGORY;
        $sub_group_label = 'Kategori Kas';
        $module = TenantPermissions::MODULE_CASH_CATEGORY;
        $module_label = 'Kategori Kas';
        Permission::firstOrCreate([
            'name' => TenantPermissions::CASH_CATEGORY_VIEW_ALL,
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
            'name' => TenantPermissions::CASH_CATEGORY_VIEW,
        ], [
            'label' => 'Lihat Detail',
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
            'name' => TenantPermissions::CASH_CATEGORY_CREATE,
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
            'name' => TenantPermissions::CASH_CATEGORY_UPDATE,
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
            'name' => TenantPermissions::CASH_CATEGORY_DELETE,
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

        // Cash Account Permissions
        $sub_group = TenantPermissions::SUBGROUP_CASH_ACCOUNT;
        $sub_group_label = 'Akun Kas';
        $module = TenantPermissions::MODULE_CASH_ACCOUNT;
        $module_label = 'Akun Kas';
        Permission::firstOrCreate([
            'name' => TenantPermissions::CASH_ACCOUNT_VIEW_ALL,
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
            'name' => TenantPermissions::CASH_ACCOUNT_VIEW,
        ], [
            'label' => 'Lihat Detail',
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
            'name' => TenantPermissions::CASH_ACCOUNT_CREATE,
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
            'name' => TenantPermissions::CASH_ACCOUNT_UPDATE,
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
            'name' => TenantPermissions::CASH_ACCOUNT_DELETE,
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

        Log::info("PermissionsCashSeeder: Completed");
    }
}

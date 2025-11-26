<?php

namespace Database\Seeders\Tenant\Permissions;

use App\Constants\TenantPermissions;
use App\Models\Tenant\Role as TenantRole;
use App\Models\Tenant\User as TenantUser;
use App\Models\Global\Merchant;
use App\Models\Tenant\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed data for tenant database
        try {
            // Call child seeders first to create permissions

            $group = TenantPermissions::GROUP_DASHBOARD;
            $group_label = 'Dashboard';
            $sub_group = TenantPermissions::SUBGROUP_DASHBOARD;
            $sub_group_label = 'Dashboard';
            $module = TenantPermissions::MODULE_DASHBOARD;
            $module_label = 'Dashboard';
            $application = TenantPermissions::APPLICATION;
            $application_label = 'Main Application';
            Permission::firstOrCreate(
                ['name' => TenantPermissions::DASHBOARD_VIEW_ALL],
                [
                    'label' => 'Dashboard',
                    'group' => $group,
                    'group_label' => $group_label,
                    'sub_group' => $sub_group,
                    'sub_group_label' => $sub_group_label,
                    'module' => $module,
                    'module_label' => $module_label,
                    'application' => $application,
                    'application_label' => $application_label,
                ]
            );
            $this->call([
                \Database\Seeders\Tenant\Permissions\PermissionsUserRoleSeeder::class,
                \Database\Seeders\Tenant\Permissions\PermissionsCashSeeder::class,
            ]);
        } catch (\Exception $e) {
            Log::error("PermissionsSeeder: Error calling child seeders - " . $e->getMessage());
            throw $e;
        }

        // Now create role and assign to merchant owner

        try {
            $db_prefix = env('DB_TENANT_PREFIX', 'gutsypos') . env('DB_TENANT_SEPARATOR', '_');

            // Ambil nama database tenant yang sebenarnya dari koneksi saat ini
            $tenantConnection = DB::connection()->getName();
            $tenantDatabase = DB::connection()->getDatabaseName();

            // Extract tenant_id dari nama database (bukan dari nama koneksi)
            $tenantId = str_replace($db_prefix, '', $tenantDatabase);

            // Pindah ke koneksi global untuk ambil merchant email
            $merchant = Merchant::on('global_mysql')->where('tenant_id', '=', $tenantId)->first();

            if (!$merchant) {
                return;
            }

            if (!$merchant->email) {
                return;
            }

            // Pindah kembali ke koneksi tenant untuk cari user dan set role
            $user = TenantUser::on($tenantConnection)->where('email', '=', $merchant->email)->first();
            if (!$user) {
                return;
            }

            // Cari atau buat role Pemilik
            $ownerRole = TenantRole::on($tenantConnection)->firstOrCreate([
                'name' => 'Pemilik',
            ], [
                'description' => 'Pemilik dengan akses penuh',
            ]);

            // assign all permissions to owner role
            $ownerRole->permissions()->sync(Permission::all()->pluck('id')->toArray());

            // Set role ke user
            $user->assignRole($ownerRole->name);
            $user->save();
        } catch (\Exception $e) {
            Log::error("PermissionsSeeder: Error creating role and assigning to user - " . $e->getMessage());
            Log::error("PermissionsSeeder: Stack trace - " . $e->getTraceAsString());
            // Don't throw, just log the error
        }
    }
}

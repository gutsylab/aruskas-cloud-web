<?php

namespace Database\Seeders\Tenant\Permissions;

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

        Log::info("PermissionsSeeder: Started");

        // Call child seeders first to create permissions
        Log::info("PermissionsSeeder: About to call child seeders");

        try {
            $this->call([
                \Database\Seeders\Tenant\Permissions\PermissionsUserRoleSeeder::class,
                \Database\Seeders\Tenant\Permissions\PermissionsCashSeeder::class,
            ]);
            Log::info("PermissionsSeeder: Child seeders completed successfully");
        } catch (\Exception $e) {
            Log::error("PermissionsSeeder: Error calling child seeders - " . $e->getMessage());
            throw $e;
        }

        // Now create role and assign to merchant owner
        Log::info("PermissionsSeeder: Create Role and Assign to Merchant Owner User");

        try {
            $db_prefix = env('DB_TENANT_PREFIX', 'gutsypos') . env('DB_TENANT_SEPARATOR', '_');

            // Ambil nama database tenant yang sebenarnya dari koneksi saat ini
            $tenantConnection = DB::connection()->getName();
            $tenantDatabase = DB::connection()->getDatabaseName();

            Log::info("PermissionsSeeder: Current tenant connection: {$tenantConnection}");
            Log::info("PermissionsSeeder: Current tenant database: {$tenantDatabase}");

            // Extract tenant_id dari nama database (bukan dari nama koneksi)
            $tenantId = str_replace($db_prefix, '', $tenantDatabase);
            Log::info("PermissionsSeeder: Derived tenant ID: {$tenantId}");

            // Pindah ke koneksi global untuk ambil merchant email
            $merchant = Merchant::on('global_mysql')->where('tenant_id', '=', $tenantId)->first();

            if (!$merchant) {
                Log::warning("PermissionsSeeder: Merchant not found for tenant_id: {$tenantId}");
                return;
            }

            if (!$merchant->email) {
                Log::warning("PermissionsSeeder: Merchant email is empty");
                return;
            }

            Log::info("PermissionsSeeder: Merchant email: {$merchant->email}");

            // Pindah kembali ke koneksi tenant untuk cari user dan set role
            $user = TenantUser::on($tenantConnection)->where('email', '=', $merchant->email)->first();

            if (!$user) {
                Log::warning("PermissionsSeeder: User not found with email: {$merchant->email}");
                return;
            }

            Log::info("PermissionsSeeder: Found user with email: {$user->email}");

            // Cari atau buat role Pemilik
            $ownerRole = TenantRole::on($tenantConnection)->firstOrCreate([
                'name' => 'Pemilik',
            ], [
                'description' => 'Pemilik dengan akses penuh',
            ]);

            // assign all permissions to owner role
            $ownerRole->permissions()->sync(Permission::all()->pluck('id')->toArray());

            Log::info("PermissionsSeeder: Role 'Pemilik' created/found with ID: {$ownerRole->id}");

            // Set role ke user
            $user->assignRole($ownerRole->name);
            $user->save();

            Log::info("PermissionsSeeder: Role assigned to user successfully");
        } catch (\Exception $e) {
            Log::error("PermissionsSeeder: Error creating role and assigning to user - " . $e->getMessage());
            Log::error("PermissionsSeeder: Stack trace - " . $e->getTraceAsString());
            // Don't throw, just log the error
        }

        Log::info("PermissionsSeeder: Completed");
    }
}

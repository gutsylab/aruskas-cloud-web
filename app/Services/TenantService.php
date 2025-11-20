<?php

namespace App\Services;

use App\Models\Global\Merchant;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Exception;

class TenantService
{
    /**
     * Get tenant database name with configurable prefix.
     */
    public function getTenantDatabaseName(string $tenantId): string
    {
        $prefix = config('database.tenant.prefix', 'tenant_');
        $separator = config('database.tenant.separator', '_');

        return $prefix . $separator . $tenantId;
    }

    /**
     * Get tenant connection name.
     */
    public function getTenantConnectionName(Merchant $merchant): string
    {
        return "tenant_{$merchant->tenant_id}";
    }
    /**
     * Set the tenant database connection for the current request.
     */
    public function setTenantConnection(Merchant $merchant): void
    {
        $connectionName = $this->getTenantConnectionName($merchant);

        // Configure the tenant database connection
        Config::set("database.connections.{$connectionName}", [
            'driver' => 'mysql',
            'host' => config('database.connections.tenant_mysql.host'),
            'port' => config('database.connections.tenant_mysql.port'),
            'database' => $merchant->database_name,
            'username' => config('database.connections.tenant_mysql.username'),
            'password' => config('database.connections.tenant_mysql.password'),
            'unix_socket' => config('database.connections.tenant_mysql.unix_socket'),
            'charset' => config('database.connections.tenant_mysql.charset'),
            'collation' => config('database.connections.tenant_mysql.collation'),
            'prefix' => config('database.connections.tenant_mysql.prefix'),
            'prefix_indexes' => config('database.connections.tenant_mysql.prefix_indexes'),
            'strict' => config('database.connections.tenant_mysql.strict'),
            'engine' => config('database.connections.tenant_mysql.engine'),
            'options' => config('database.connections.tenant_mysql.options'),
        ]);

        // Set as default connection
        Config::set('database.default', $connectionName);

        // Purge the connection to force reconnection
        DB::purge($connectionName);
    }

    /**
     * Create a new tenant database and run migrations.
     */
    public function createTenant(Merchant $merchant): bool
    {
        try {
            // Create database
            $this->createTenantDatabase($merchant->database_name);

            // Set tenant connection
            $this->setTenantConnection($merchant);

            // Run migrations for tenant
            $this->runTenantMigrations($merchant);

            return true;
        } catch (Exception $e) {
            throw new Exception("Failed to create tenant: " . $e->getMessage());
        }
    }

    /**
     * Create tenant database.
     */
    private function createTenantDatabase(string $databaseName): void
    {
        // Use the global connection to create the database
        $globalConnection = config('database.global');

        DB::connection($globalConnection)->statement(
            "CREATE DATABASE IF NOT EXISTS `{$databaseName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
        );
    }

    /**
     * Run migrations for tenant database.
     */
    private function runTenantMigrations(Merchant $merchant): void
    {
        $connectionName = $this->getTenantConnectionName($merchant);

        // Run tenant-specific migrations
        Artisan::call('migrate', [
            '--database' => $connectionName,
            '--path' => 'database/migrations/tenant',
            '--force' => true,
        ]);
    }

    /**
     * Drop tenant database.
     */
    public function dropTenant(Merchant $merchant): bool
    {
        try {
            $globalConnection = config('database.global');

            DB::connection($globalConnection)->statement(
                "DROP DATABASE IF EXISTS `{$merchant->database_name}`"
            );

            return true;
        } catch (Exception $e) {
            throw new Exception("Failed to drop tenant database: " . $e->getMessage());
        }
    }

    /**
     * Get current tenant from request.
     */
    public function getCurrentTenant(): ?Merchant
    {
        $tenantId = $this->getTenantIdFromPath();

        if (!$tenantId) {
            return null;
        }

        // First try to find by tenant_id
        $tenant = Merchant::where('tenant_id', $tenantId)
            ->where('status', true)
            ->first();

        // Fallback to slug for backward compatibility
        if (!$tenant) {
            $tenant = Merchant::where('slug', $tenantId)
                ->where('status', true)
                ->first();
        }

        return $tenant;
    }

    /**
     * Extract tenant ID from URL path.
     */
    public function getTenantIdFromPath(): ?string
    {
        $path = request()->path();
        $segments = explode('/', $path);

        // For API routes: api/{tenant_id}/v1/...
        // Check if path starts with 'api' and second segment is tenant ID
        if (!empty($segments[0]) && $segments[0] === 'api' && !empty($segments[1]) && $this->isValidTenantId($segments[1])) {
            return $segments[1];
        }

        // For web routes: {tenant_id}/...
        // Check if first segment looks like a tenant ID
        if (!empty($segments[0]) && $this->isValidTenantId($segments[0])) {
            return $segments[0];
        }

        return null;
    }

    /**
     * Check if string is a valid tenant ID format.
     */
    private function isValidTenantId(string $id): bool
    {
        // Tenant ID should be alphanumeric and between 6-12 characters
        return preg_match('/^[A-Z0-9]{6,12}$/i', $id) === 1;
    }

    /**
     * Check if tenant database exists.
     */
    public function tenantDatabaseExists(string $databaseName): bool
    {
        try {
            $globalConnection = config('database.global');

            $result = DB::connection($globalConnection)
                ->select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$databaseName]);

            return !empty($result);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Reset to global connection.
     */
    public function resetToGlobalConnection(): void
    {
        Config::set('database.default', config('database.global'));
    }
}

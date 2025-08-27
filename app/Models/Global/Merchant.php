<?php

namespace App\Models\Global;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Merchant extends Model
{
    use HasFactory;

    /**
     * The connection name for the model.
     */
    protected $connection = 'global_mysql';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'slug',
        'tenant_id',
        'domain',
        'database_name',
        'logo',
        'address',
        'phone',
        'email',
        'website',
        'status',
        'settings',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'settings' => 'array',
        'status' => 'boolean',
    ];

    /**
     * Get the merchant's users.
     */
    public function users(): HasMany
    {
        return $this->hasMany(MerchantUser::class);
    }

    /**
     * Get the merchant's active subscription.
     */
    public function activeSubscription(): HasOne
    {
        return $this->hasOne(MerchantSubscription::class)
            ->where('status', 'active')
            ->latest();
    }

    /**
     * Get all merchant subscriptions.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(MerchantSubscription::class);
    }

    /**
     * Get the database connection for this merchant.
     */
    public function getDatabaseConnection(): string
    {
        return 'tenant_' . $this->tenant_id;
    }

    /**
     * Get the tenant database name with configurable prefix.
     */
    public function getTenantDatabaseName(): string
    {
        $prefix = config('database.tenant.prefix', 'tenant_');
        $separator = config('database.tenant.separator', '_');
        return $prefix . $separator . $this->tenant_id;
    }

    /**
     * Check if merchant is active.
     */
    public function isActive(): bool
    {
        return $this->status && $this->activeSubscription()->exists();
    }

    /**
     * Generate unique slug from name.
     */
    public static function generateSlug(string $name): string
    {
        $slug = Str::slug($name);
        $count = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = Str::slug($name) . '-' . $count;
            $count++;
        }

        return $slug;
    }

    /**
     * Generate unique random tenant ID.
     */
    public static function generateTenantId(): string
    {
        do {
            $tenantId = strtoupper(Str::random(8));
        } while (static::where('tenant_id', $tenantId)->exists());

        return $tenantId;
    }

    /**
     * Generate database name from tenant_id with configurable prefix.
     */
    public static function generateDatabaseName(string $tenantId): string
    {
        $prefix = config('database.tenant.prefix', 'tenant_');
        $separator = config('database.tenant.separator', '_');
        return $prefix . $separator . $tenantId;
    }
}

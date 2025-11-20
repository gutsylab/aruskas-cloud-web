<?php

namespace App\Models\Global;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class MerchantUser extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The connection name for the model.
     */
    protected $connection = 'global_mysql';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'merchant_id',
        'name',
        'email',
        'password',
        'role',
        'permissions',
        'is_active',
        'last_login_at',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
        'permissions' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the merchant that owns the user.
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    /**
     * Check if user is super admin (merchant owner).
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'admin']);
    }

    /**
     * Check if user has specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return in_array($permission, $this->permissions ?? []);
    }

    /**
     * Get user's available permissions based on role.
     */
    public function getAvailablePermissions(): array
    {
        $basePermissions = [
            'view_dashboard',
            'manage_profile',
        ];

        $rolePermissions = [
            'super_admin' => ['*'], // All permissions
            'admin' => [
                'manage_users',
                'manage_settings',
                'view_reports',
                'manage_content',
            ],
            'manager' => [
                'view_reports',
                'manage_content',
            ],
            'staff' => [
                'view_content',
            ],
        ];

        if ($this->role === 'super_admin') {
            return ['*'];
        }

        return array_merge(
            $basePermissions,
            $rolePermissions[$this->role] ?? []
        );
    }

    /**
     * Update last login timestamp.
     */
    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }
}

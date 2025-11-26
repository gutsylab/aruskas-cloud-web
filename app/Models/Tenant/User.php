<?php

namespace App\Models\Tenant;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, BelongsToTenant, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', '=', $roleName)->exists();
    }
    public function assignRole(string $roleName): void
    {
        $role = Role::where('name', $roleName)->first();
        if ($role) {
            $this->roles()->syncWithoutDetaching([$role->id]);
        } else {
            Log::warning("Role '{$roleName}' not found when assigning to user ID {$this->id}");
        }
    }

    public function hasPermission(string $permissionName): bool
    {
        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permissionName) {
                $query->where('name', '=', $permissionName);
            })->exists();
    }

    public function hasPermissionApplication(string $applicationName): bool
    {
        return $this->roles()
            ->whereHas('permissions', function ($query) use ($applicationName) {
                $query->where('application', '=', $applicationName);
            })->exists();
    }

    public function hasPermissionGroup(string $groupName): bool
    {
        return $this->roles()
            ->whereHas('permissions', function ($query) use ($groupName) {
                $query->where('group', '=', $groupName);
            })->exists();
    }

    public function hasPermissionSubGroup(string $subGroupName): bool
    {
        return $this->roles()
            ->whereHas('permissions', function ($query) use ($subGroupName) {
                $query->where('sub_group', '=', $subGroupName);
            })->exists();
    }

    public function hasPermissionModule(string $moduleName): bool
    {
        return $this->roles()
            ->whereHas('permissions', function ($query) use ($moduleName) {
                $query->where('module', '=', $moduleName);
            })->exists();
    }
}

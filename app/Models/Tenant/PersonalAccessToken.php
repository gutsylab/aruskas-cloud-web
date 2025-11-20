<?php

namespace App\Models\Tenant;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;
use Laravel\Sanctum\NewAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'token',
        'abilities',
        'expires_at',
        'tokenable_type',
        'tokenable_id',
    ];

    /**
     * Get the current connection name for the model.
     *
     * @return string|null
     */
    public function getConnectionName()
    {
        // Use tenant connection from session if available
        if (session()->has('tenant_connection')) {
            return session('tenant_connection');
        }

        // Fallback to default connection
        return parent::getConnectionName();
    }

    /**
     * Create a new personal access token without ID prefix.
     *
     * @param  mixed  $tokenable  The user model
     * @param  string  $name
     * @param  array  $abilities
     * @param  \DateTimeInterface|null  $expiresAt
     * @return \Laravel\Sanctum\NewAccessToken
     */
    public static function createTokenWithoutPrefix($tokenable, $name, array $abilities = ['*'], $expiresAt = null)
    {
        $plainTextToken = static::generateTokenString();

        // Get the connection from the tokenable model (user)
        $connection = $tokenable->getConnectionName();

        $token = static::on($connection)->create([
            'tokenable_type' => get_class($tokenable),
            'tokenable_id' => $tokenable->getKey(),
            'name' => $name,
            'token' => hash('sha256', $plainTextToken),
            'abilities' => $abilities,
            'expires_at' => $expiresAt,
        ]);

        return new NewAccessToken($token, $plainTextToken);
    }

    /**
     * Generate a random token string.
     *
     * @return string
     */
    protected static function generateTokenString()
    {
        return bin2hex(random_bytes(40));
    }
}

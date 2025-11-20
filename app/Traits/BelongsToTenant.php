<?php

namespace App\Traits;

trait BelongsToTenant
{
    /**
     * Boot the trait.
     */
    public static function bootBelongsToTenant(): void
    {
        // Automatically set tenant connection for tenant models
        static::creating(function ($model) {
            if (!$model->getConnection()) {
                $tenant = request()->attributes->get('tenant');
                if ($tenant) {
                    $connectionName = "tenant_{$tenant->tenant_id}";
                    $model->setConnection($connectionName);
                }
            }
        });
    }

    /**
     * Get the tenant connection name.
     */
    public function getTenantConnection(): ?string
    {
        $tenant = request()->attributes->get('tenant');
        
        if (!$tenant) {
            return null;
        }

        return "tenant_{$tenant->tenant_id}";
    }

    /**
     * Set the tenant connection for this model.
     */
    public function setTenantConnection(): void
    {
        $connectionName = $this->getTenantConnection();
        
        if ($connectionName) {
            $this->setConnection($connectionName);
        }
    }
}

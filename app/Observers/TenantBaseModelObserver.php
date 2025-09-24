<?php

namespace App\Observers;

use App\Traits\BelongsToTenant;
use Illuminate\Support\Facades\Auth;

class TenantBaseModelObserver
{
    /**
     * Handle the model "creating" event.
     */
    public function creating($model)
    {
        // Only apply if user is authenticated and in tenant context
        if ($this->shouldApplyAudit($model)) {
            $userId = Auth::id();
            $model->created_by_id = $userId;
            $model->updated_by_id = $userId;
        }
    }

    /**
     * Handle the model "updating" event.
     */
    public function updating($model)
    {
        // Only apply if user is authenticated and in tenant context
        if ($this->shouldApplyAudit($model)) {
            $model->updated_by_id = Auth::id();
        }
    }

    /**
     * Handle the model "deleting" event.
     */
    public function deleting($model)
    {
        // Only apply if user is authenticated and in tenant context
        if ($this->shouldApplyAudit($model)) {
            $model->deleted_by_id = Auth::id();
        }
    }

    /**
     * Check if audit should be applied.
     */
    private function shouldApplyAudit($model): bool
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return false;
        }

        // Check if we're in tenant context
        if (!request()->attributes->has('tenant')) {
            return false;
        }

        // Check if model has audit columns
        return $this->hasAuditColumns($model);
    }

    /**
     * Check if model has audit columns.
     */
    private function hasAuditColumns($model): bool
    {
        return in_array('created_by_id', $model->getFillable()) &&
               in_array('updated_by_id', $model->getFillable()) &&
               in_array('deleted_by_id', $model->getFillable());
    }
}

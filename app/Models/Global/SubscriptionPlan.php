<?php

namespace App\Models\Global;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
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
        'description',
        'price',
        'billing_cycle',
        'features',
        'limits',
        'status',
        'trial_days',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'features' => 'array',
        'limits' => 'array',
        'status' => 'boolean',
        'price' => 'decimal:2',
        'trial_days' => 'integer',
    ];

    /**
     * Get the plan's subscriptions.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(MerchantSubscription::class);
    }

    /**
     * Check if plan is active.
     */
    public function isActive(): bool
    {
        return $this->status;
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2);
    }
}

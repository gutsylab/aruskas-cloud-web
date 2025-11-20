<?php

namespace App\Models\Global;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class MerchantSubscription extends Model
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
        'merchant_id',
        'subscription_plan_id',
        'status',
        'starts_at',
        'ends_at',
        'trial_ends_at',
        'last_payment_at',
        'next_payment_at',
        'canceled_at',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'last_payment_at' => 'datetime',
        'next_payment_at' => 'datetime',
        'canceled_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the merchant that owns the subscription.
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    /**
     * Get the subscription plan.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    /**
     * Check if subscription is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active' &&
               $this->ends_at->isFuture();
    }

    /**
     * Check if subscription is in trial period.
     */
    public function onTrial(): bool
    {
        return $this->trial_ends_at &&
               $this->trial_ends_at->isFuture();
    }

    /**
     * Check if subscription is expired.
     */
    public function isExpired(): bool
    {
        return $this->ends_at->isPast();
    }

    /**
     * Check if subscription is canceled.
     */
    public function isCanceled(): bool
    {
        return $this->status === 'canceled' ||
               $this->canceled_at !== null;
    }

    /**
     * Get days remaining in subscription.
     */
    public function daysRemaining(): int
    {
        if ($this->ends_at->isPast()) {
            return 0;
        }

        return $this->ends_at->diffInDays(now());
    }

    /**
     * Cancel the subscription.
     */
    public function cancel(): void
    {
        $this->update([
            'status' => 'canceled',
            'canceled_at' => now(),
        ]);
    }

    /**
     * Renew the subscription.
     */
    public function renew(int $days = 30): void
    {
        $days = $days ?? 30; // Default to 30 days

        $this->update([
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addDays($days),
            'next_payment_at' => now()->addDays($days),
            'canceled_at' => null,
        ]);
    }
}

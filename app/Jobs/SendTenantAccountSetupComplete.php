<?php

namespace App\Jobs;

use App\Mail\TenantAccountSetupComplete;
use App\Models\Global\Merchant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendTenantAccountSetupComplete implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The merchant instance.
     */
    public Merchant $merchant;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(Merchant $merchant)
    {
        $this->merchant = $merchant;
    }

    /**
     * Execute the job.
     *
     * Send account setup completion email with login info and email verification link.
     */
    public function handle(): void
    {
        // Send the account setup complete email with verification link
        Mail::to($this->merchant->email)->send(new TenantAccountSetupComplete($this->merchant));

        Log::info("Sent account setup complete email to {$this->merchant->email} for tenant {$this->merchant->tenant_id}");
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Failed to send tenant account setup complete email', [
            'merchant_id' => $this->merchant->id,
            'tenant_id' => $this->merchant->tenant_id,
            'email' => $this->merchant->email,
            'error' => $exception->getMessage(),
        ]);
    }
}

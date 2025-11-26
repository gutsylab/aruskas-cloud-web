<?php

namespace App\Jobs;

use App\Mail\TenantEmailVerification;
use App\Models\Global\Merchant;
use App\Services\TenantService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendTenantEmailVerification implements ShouldQueue
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

        // Job will be queued in global database (default connection)
        // The job itself will switch to tenant connection when executed
    }

    /**
     * Execute the job.
     */
    public function handle(TenantService $tenantService): void
    {
        // Switch to tenant connection to ensure email is sent from tenant context
        // $tenantService->setTenantConnection($this->merchant);

        // Send the email verification
        Mail::to($this->merchant->email)->send(new TenantEmailVerification($this->merchant));

        // Reset to global connection
        // $tenantService->resetToGlobalConnection();
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        // Log the failure or notify administrators
        \Log::error('Failed to send tenant email verification', [
            'merchant_id' => $this->merchant->id,
            'tenant_id' => $this->merchant->tenant_id,
            'email' => $this->merchant->email,
            'error' => $exception->getMessage(),
        ]);
    }
}

<?php

namespace App\Mail;

use App\Models\Global\Merchant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;

class TenantAccountSetupComplete extends Mailable
{
    use Queueable, SerializesModels;

    public $merchant;
    public $verificationUrl;
    public $loginUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Merchant $merchant)
    {
        $this->merchant = $merchant;
        $this->verificationUrl = $this->generateVerificationUrl($merchant);
        $this->loginUrl = url("/{$merchant->tenant_id}/login");
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Setup Akun Selesai - Selamat Datang di ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.tenant-account-setup-complete',
            with: [
                'merchant' => $this->merchant,
                'verificationUrl' => $this->verificationUrl,
                'loginUrl' => $this->loginUrl,
                'tenantId' => $this->merchant->tenant_id,
                'email' => $this->merchant->email,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Generate the email verification URL.
     */
    protected function generateVerificationUrl(Merchant $merchant): string
    {
        return URL::temporarySignedRoute(
            'tenant.email.verify',
            Carbon::now()->addHours(24), // Link expires in 24 hours
            [
                'id' => $merchant->id,
                'hash' => sha1($merchant->email),
            ]
        );
    }
}

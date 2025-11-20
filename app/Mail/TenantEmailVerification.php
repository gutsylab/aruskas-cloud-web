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

class TenantEmailVerification extends Mailable
{
    use Queueable, SerializesModels;

    public $merchant;
    public $verificationUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Merchant $merchant)
    {
        $this->merchant = $merchant;
        $this->verificationUrl = $this->generateVerificationUrl($merchant);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verifikasi Email - ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.tenant-email-verification',
            with: [
                'merchant' => $this->merchant,
                'verificationUrl' => $this->verificationUrl,
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

<?php

namespace App\Jobs;

use App\Models\EmailMessage;
use App\Models\EmailProvider;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\Mailer;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Email as M;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 4;
    public $backoff = [10, 30, 120];
    public int $timeout = 60;

    public function __construct(
        public int $messageId,
        public ?array $smtpInline = null
    ) {}

    public function handle(): void
    {
        $msg = EmailMessage::findOrFail($this->messageId);
        $prov = $msg->email_provider_id ? EmailProvider::find($msg->email_provider_id) : null;

        [$transport, $fromNameProvider] = $this->buildTransport($prov?->type, $prov?->credentials, $this->smtpInline);


        // ambil from_name: prioritas request > provider > null
        // PRIORITAS: request (email_messages.from_name) > fromNameProvider > credentials[from_name]
        $fromName =
            $msg->from_name
            ?? $fromNameProvider
            ?? ($prov?->credentials['from_name'] ?? null);

        $mail = (new M())
            ->from(new Address($msg->from_email, $fromName ?? ''))
            ->subject($msg->subject);

        foreach ($msg->to as $a) {
            $mail->addTo($a);
        }
        foreach ($msg->cc ?? [] as $a) {
            $mail->addCc($a);
        }
        foreach ($msg->bcc ?? [] as $a) {
            $mail->addBcc($a);
        }

        if ($msg->html) {
            $mail->html($msg->html);
        }
        if ($msg->text) {
            $mail->text($msg->text);
        }

        foreach (($msg->attachments ?? []) as $att) {
            $content = Storage::disk('local')->get($att['path']);
            $mail->attach($content, $att['name'] ?? basename($att['path']));
        }

        (new Mailer($transport))->send($mail);

        $msg->update(['status' => 'sent', 'sent_at' => now(), 'error' => null]);
    }

    private function buildTransport(?string $type, ?array $cred, ?array $inline)
    {
        if ($inline) {
            $enc = $inline['encryption'] ?? 'tls';
            $t = new EsmtpTransport(
                $inline['host'],
                (int) $inline['port'],
                $enc === 'ssl' ? true : ($enc === 'tls' ? 'tls' : null)
            );
            $t->setUsername($inline['username']);
            $t->setPassword($inline['password']);
            return [$t, null];
        }

        if ($type === 'smtp') {
            $enc = $cred['encryption'] ?? 'tls';
            $t = new EsmtpTransport(
                $cred['host'],
                (int) $cred['port'],
                $enc === 'ssl' ? true : ($enc === 'tls' ? 'tls' : null)
            );
            $t->setUsername($cred['username']);
            $t->setPassword($cred['password']);
            return [$t, $cred['from_name'] ?? null];
        }

        // TODO: implementasi provider API (SES/Mailgun/Postmark/Resend) bila diperlukan
        throw new \RuntimeException('Provider type belum diimplementasi');
    }

    public function failed(\Throwable $e): void
    {
        EmailMessage::whereKey($this->messageId)->update([
            'status' => 'failed',
            'error' => $e->getMessage(),
        ]);
    }
}

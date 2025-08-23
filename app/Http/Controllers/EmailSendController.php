<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmailJob;
use App\Models\EmailMessage;
use Illuminate\Http\Request;
use App\Models\EmailProvider;
use Illuminate\Http\JsonResponse;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email as M;
use App\Http\Requests\SendEmailRequest;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

class EmailSendController extends Controller
{
    public function send(SendEmailRequest $req)
    {
        $client = $req->attributes->get('api_client');

        $msg = EmailMessage::create([
            'user_id' => $client->id,
            'email_provider_id' => $req->provider_id,
            'from_email' => $req->from,
            'from_name' => $req->from_name ?? '',
            'to' => $req->to,
            'cc' => $req->cc ?? [],
            'bcc' => $req->bcc ?? [],
            'subject' => $req->subject,
            'html' => $req->html,
            'text' => $req->text,
            'attachments' => $req->attachments ?? [],
            'status' => 'queued',
        ]);

        SendEmailJob::dispatch($msg->id, $req->input('smtp'))->onQueue('emails');

        return response()->json([
            'message_id' => $msg->id,
            'status' => 'queued',
        ], 202);
    }

    public function show($id)
    {
        $client = request()->attributes->get('api_client');
        $m = EmailMessage::findOrFail($id);
        abort_unless($m->user_id === $client->id, 404);
        return response()->json($m);
    }

    public function sendNow(SendEmailRequest $req): JsonResponse
    {
        $client = $req->attributes->get('api_client');

        // batas waktu eksekusi request sinkron (opsional)
        @set_time_limit((int)($req->integer('timeout_sec', 30)));

        // simpan log dulu supaya tetap ada jejak meski gagal
        $msg = EmailMessage::create([
            'user_id' => $client->id,
            'email_provider_id' => $req->provider_id,
            'from_email' => $req->from,
            'from_name' => $req->from_name ?? '',
            'to' => $req->to,
            'cc' => $req->cc ?? [],
            'bcc' => $req->bcc ?? [],
            'subject' => $req->subject,
            'html' => $req->html,
            'text' => $req->text,
            'attachments' => $req->attachments ?? [],
            'status' => 'processing',
        ]);

        try {
            // pilih kredensial: provider tersimpan atau inline SMTP
            $provider = $msg->email_provider_id ? EmailProvider::find($msg->email_provider_id) : null;
            [$transport, $fromNameProvider] = $this->buildTransport(
                $provider?->type,
                $provider?->credentials,
                $req->input('smtp') // inline smtp
            );

            // ambil from_name: prioritas request > provider > null
            $fromName = $req->input('from_name')
                ?? ($fromNameProvider ?? ($provider->credentials['from_name'] ?? null));

            // compose email
            $mail = (new M())
                ->from(new Address($msg->from_email, $fromName))
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

            if ($msg->html) $mail->html($msg->html);
            if ($msg->text) $mail->text($msg->text);

            foreach (($msg->attachments ?? []) as $att) {
                $content = Storage::disk('local')->get($att['path']);
                $mail->attach($content, $att['name'] ?? basename($att['path']));
            }

            // kirim sinkron
            (new Mailer($transport))->send($mail);

            // ambil Message-ID yang di-set oleh transport (jika ada)
            $smtpMessageId = optional($mail->getHeaders()->get('Message-ID'))->getBodyAsString();

            $msg->update([
                'status'  => 'sent',
                'sent_at' => now(),
                'error'   => null,
            ]);

            return response()->json([
                'message_id'      => $msg->id,
                'status'          => 'sent',
                'smtp_message_id' => $smtpMessageId,
            ], 200);
        } catch (\Throwable $e) {
            $msg->update([
                'status' => 'failed',
                'error'  => $e->getMessage(),
            ]);

            return response()->json([
                'message_id' => $msg->id,
                'status'     => 'failed',
                'error'      => $e->getMessage(),
            ], 500);
        }
    }

    /** Sama seperti di Job: bangun transport per-request */
    private function buildTransport(?string $type, ?array $cred, ?array $inline)
    {
        if ($inline) {
            $enc = $inline['encryption'] ?? 'tls'; // 587=tls, 465=ssl
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

        throw new \RuntimeException('Provider type belum diimplementasi untuk sinkron.');
    }
}

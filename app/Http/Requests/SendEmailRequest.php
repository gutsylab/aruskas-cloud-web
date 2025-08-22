<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'provider_id' => ['nullable', 'exists:email_providers,id'],
            'from' => ['required', 'email'],
            'from_name' => ['nullable', 'string', 'max:255'],
            'to'   => ['required', 'array', 'min:1'],
            'to.*' => ['email'],
            'cc'   => ['array'],
            'cc.*' => ['email'],
            'bcc'  => ['array'],
            'bcc.*' => ['email'],
            'subject' => ['required', 'string', 'max:998'],
            'html'    => ['nullable', 'string'],
            'text'    => ['nullable', 'string'],

            'attachments' => ['array'],
            'attachments.*.path' => ['required_with:attachments', 'string'],
            'attachments.*.name' => ['nullable', 'string'],

            // Opsi B: langsung SMTP
            'smtp' => ['array'],
            'smtp.host'     => ['required_without:provider_id', 'string'],
            'smtp.port'     => ['required_without:provider_id', 'integer'],
            'smtp.username' => ['required_without:provider_id', 'string'],
            'smtp.password' => ['required_without:provider_id', 'string'],
            'smtp.encryption' => ['nullable', 'in:tls,ssl,null'],
        ];
    }
}

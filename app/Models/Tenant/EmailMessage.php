<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class EmailMessage extends Model
{
    protected $fillable = [
        'user_id',
        'email_provider_id',
        'from_email',
        'from_name',
        'to',
        'cc',
        'bcc',
        'subject',
        'html',
        'text',
        'attachments',
        'status',
        'error',
        'sent_at'
    ];

    protected $casts = [
        'to' => 'array',
        'cc' => 'array',
        'bcc' => 'array',
        'attachments' => 'array',
        'sent_at' => 'datetime',
    ];
}

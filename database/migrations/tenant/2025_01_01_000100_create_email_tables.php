<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('email_providers', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained('api_clients')->cascadeOnDelete();
            $t->string('name');
            $t->enum('type', ['smtp', 'ses', 'mailgun', 'postmark', 'resend']);
            $t->text('credentials'); // encrypted:array
            $t->timestamps();
        });

        Schema::create('email_messages', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained('api_clients')->cascadeOnDelete();
            $t->foreignId('email_provider_id')->nullable()->constrained()->nullOnDelete();

            $t->string('from_email');
            $t->string('from_name')->nullable();
            $t->json('to');
            $t->json('cc')->nullable();
            $t->json('bcc')->nullable();
            $t->string('subject');
            $t->longText('html')->nullable();
            $t->longText('text')->nullable();

            $t->json('attachments')->nullable(); // [{path,name?}]
            $t->string('status')->default('queued'); // queued|sent|failed
            $t->text('error')->nullable();
            $t->timestamp('sent_at')->nullable();
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_messages');
        Schema::dropIfExists('email_providers');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Get the migration connection name.
     */
    public function getConnection()
    {
        return config('database.global');
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection($this->getConnection())->create('merchant_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained('merchants')->onDelete('cascade');
            $table->foreignId('subscription_plan_id')->constrained('subscription_plans')->onDelete('cascade');
            $table->enum('status', ['active', 'inactive', 'canceled', 'expired'])->default('active');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('last_payment_at')->nullable();
            $table->timestamp('next_payment_at')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->getConnection())->dropIfExists('merchant_subscriptions');
    }
};

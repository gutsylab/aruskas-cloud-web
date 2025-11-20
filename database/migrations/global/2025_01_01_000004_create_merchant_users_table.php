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
        Schema::connection($this->getConnection())->create('merchant_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained('merchants')->onDelete('cascade');
            $table->string('name');
            $table->string('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['super_admin', 'admin', 'manager', 'staff'])->default('staff');
            $table->json('permissions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->unique(['merchant_id', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->getConnection())->dropIfExists('merchant_users');
    }
};

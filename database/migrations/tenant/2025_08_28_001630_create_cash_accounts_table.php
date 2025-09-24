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
        Schema::create('cash_accounts', function (Blueprint $table) {
            $table->id();

            $table->char('account_number', 12)->unique()->index();
            $table->string('name');
            $table->text('description')->nullable();

            $table->timestamps();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->onDelete('no action');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->onDelete('no action');

            $table->softDeletes();
            $table->foreignId('deleted_by_id')->nullable()->constrained('users')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_accounts');
    }
};

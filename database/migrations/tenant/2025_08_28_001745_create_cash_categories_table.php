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
        Schema::create('cash_categories', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->enum('type', ['income', 'expense'])->default('income');
            $table->text('description')->nullable();

            $table->unique(['name', 'type'], 'name_type_unique');

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
        Schema::dropIfExists('cash_categories');
    }
};

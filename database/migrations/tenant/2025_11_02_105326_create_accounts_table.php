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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();

            $table->string('code')->unique();
            $table->integer('sort')->default(1);
            $table->string('name');
            $table->enum('type', [
                'asset',
                'liability',
                'equity',
                'revenue',
                'cost_of_goods_sold', // ✅ ditambahkan di sini
                'expense',
                'other_income',
                'other_expense',
            ]);
            $table->boolean('is_cash')->default(false);
            $table->enum('cash_flow_type', ['in', 'out', 'both'])->default('both');

            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};

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
        Schema::table('sequences', function (Blueprint $table) {
            $table->id();
            $table->string('code'); // e.g. cash_flow, sales, purchase
            $table->string('name'); // Nama sequence, e.g. "Kas Masuk", "Penjualan"
            $table->string('prefix')->nullable(); // optional prefix
            $table->string('pattern')->default('{PREFIX}/{YYYY}/{MM}/{####}'); // pola format

            $table->enum('reset_period', ['none', 'year', 'month', 'day'])->default('month');

            $table->unsignedBigInteger('number')->default(0);

            $table->string('year', 4)->nullable();
            $table->string('month', 2)->nullable();
            $table->string('day', 2)->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->unique(['code', 'year', 'month', 'day']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sequences', function (Blueprint $table) {
            Schema::dropIfExists('sequences');
        });
    }
};

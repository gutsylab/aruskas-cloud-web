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
        Schema::create('api_clients', function (Blueprint $table) {
            $table->id();
            
            $table->string('name');
            $table->string('key_hash', 128)->unique(); // hash dari API Key
            $table->json('ip_allowlist')->nullable();  // ["1.2.3.4","5.6.7.0/24"]
            $table->unsignedInteger('rate_per_min')->default(120);
            $table->boolean('active')->default(true);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_clients');
    }
};

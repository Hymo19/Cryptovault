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
    Schema::create('applications', function (Blueprint $table) {
        $table->id();
        $table->foreignId('tenant_id')
              ->constrained()
              ->onDelete('cascade');
        $table->string('name');
        $table->string('description')->nullable();
        $table->enum('status', ['active', 'suspended'])
              ->default('active');
        $table->unsignedBigInteger('total_encryptions')->default(0);
        $table->unsignedBigInteger('total_decryptions')->default(0);
        $table->timestamp('last_used_at')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};

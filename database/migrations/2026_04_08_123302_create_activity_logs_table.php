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
    Schema::create('activity_logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('tenant_id')
              ->constrained()
              ->onDelete('cascade');
        $table->foreignId('application_id')
              ->nullable()
              ->constrained()
              ->onDelete('set null');
        $table->enum('action', ['encrypt', 'decrypt', 'rotate', 'init', 'login', 'revoke']);
        $table->string('ip_address')->nullable();
        $table->enum('status', ['success', 'failed'])
              ->default('success');
        $table->text('message')->nullable();
        $table->timestamp('performed_at')->useCurrent();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};

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
    Schema::create('plans', function (Blueprint $table) {
        $table->id();
        $table->string('name');                        // Free / Pro / Enterprise
        $table->integer('max_apps')->default(1);       // Nombre max d'applications
        $table->integer('max_ops_per_month')->default(1000); // Opérations max/mois
        $table->decimal('price', 10, 2)->default(0);   // Prix mensuel
        $table->text('description')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};

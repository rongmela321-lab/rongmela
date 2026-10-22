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
        Schema::create('tailors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone_number', 50)->nullable();
            $table->enum('type', ['monthly', 'percentage'])->default('percentage');
            $table->decimal('percentage', 8, 2)->default(0);
            $table->decimal('monthly_rate', 12, 2)->default(0);
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tailors');
    }
};

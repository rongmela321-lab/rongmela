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
        Schema::create('tailor_deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->unique();
            $table->unsignedBigInteger('tailor_id')->index();
            $table->date('delivery_date');
            $table->decimal('delivery_amount', 12, 2)->default(0);
            $table->decimal('percentage', 8, 2)->default(0);
            $table->decimal('owner_get', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('due_amount', 12, 2)->default(0);
            $table->enum('payment_status', ['paid', 'partial', 'due'])->default('due');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('tailor_id')->references('id')->on('tailors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tailor_deliveries');
    }
};

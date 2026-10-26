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
        if (!Schema::hasTable('capital_deposits')) {
            Schema::create('capital_deposits', function (Blueprint $table) {
                $table->id();
                $table->string('reference_no')->unique();
                $table->unsignedInteger('account_id');
                $table->double('amount', 15, 2);
                $table->string('type')->default('deposit'); // deposit or withdraw
                $table->unsignedBigInteger('user_id')->nullable();
                $table->text('note')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capital_deposits');
    }
};


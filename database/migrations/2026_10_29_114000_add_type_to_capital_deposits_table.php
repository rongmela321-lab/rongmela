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
        if (Schema::hasTable('capital_deposits')) {
            if (!Schema::hasColumn('capital_deposits', 'type')) {
                Schema::table('capital_deposits', function (Blueprint $table) {
                    $table->string('type')->default('deposit')->after('amount');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('capital_deposits')) {
            if (Schema::hasColumn('capital_deposits', 'type')) {
                Schema::table('capital_deposits', function (Blueprint $table) {
                    $table->dropColumn('type');
                });
            }
        }
    }
};


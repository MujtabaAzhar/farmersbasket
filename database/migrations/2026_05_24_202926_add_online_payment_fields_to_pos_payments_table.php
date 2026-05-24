<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: expand the enum to include the new value alongside the old ones
        DB::statement("ALTER TABLE pos_payments MODIFY COLUMN method ENUM('cash','card','bank_transfer','split','online_transfer') NOT NULL DEFAULT 'cash'");
        // Step 2: migrate legacy rows to the new value
        DB::statement("UPDATE pos_payments SET method = 'online_transfer' WHERE method IN ('card','bank_transfer')");
        // Step 3: drop the now-unused legacy values
        DB::statement("ALTER TABLE pos_payments MODIFY COLUMN method ENUM('cash','online_transfer','split') NOT NULL DEFAULT 'cash'");

        Schema::table('pos_payments', function (Blueprint $table) {
            $table->string('online_platform', 50)->nullable()->after('reference_no');
            $table->boolean('payment_verified')->nullable()->after('online_platform');
        });
    }

    public function down(): void
    {
        Schema::table('pos_payments', function (Blueprint $table) {
            $table->dropColumn(['online_platform', 'payment_verified']);
        });
        DB::statement("ALTER TABLE pos_payments MODIFY COLUMN method ENUM('cash','card','bank_transfer','split') NOT NULL DEFAULT 'cash'");
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE pos_payments MODIFY COLUMN method ENUM('cash','online_transfer','credit','split') NOT NULL DEFAULT 'cash'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE pos_payments MODIFY COLUMN method ENUM('cash','online_transfer','split') NOT NULL DEFAULT 'cash'");
    }
};

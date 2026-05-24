<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Adds POS-specific role & branch to the users table.
// utype ('ADM') is preserved unchanged for backward compatibility.
// pos_role = NULL means regular customer/non-POS user.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('pos_role', ['pos_supervisor', 'cashier'])->nullable()->after('utype');
            $table->foreignId('branch_id')->nullable()->after('pos_role')->constrained('branches')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn(['pos_role', 'branch_id']);
        });
    }
};

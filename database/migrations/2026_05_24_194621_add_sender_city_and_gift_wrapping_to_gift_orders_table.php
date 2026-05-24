<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gift_orders', function (Blueprint $table) {
            $table->string('sender_city')->nullable()->after('sender_address');
            $table->boolean('gift_wrapping')->default(false)->after('gift_message');
        });
    }

    public function down(): void
    {
        Schema::table('gift_orders', function (Blueprint $table) {
            $table->dropColumn(['sender_city', 'gift_wrapping']);
        });
    }
};

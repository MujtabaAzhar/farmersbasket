<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Extends the shared orders table to support POS-origin orders.
// source='pos' identifies POS orders; cashier_id / branch_id / pos_session_id
// provide shift-level accountability. is_hold allows order parking.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('source', ['ecommerce', 'pos'])->default('ecommerce')->after('type');
            $table->unsignedBigInteger('cashier_id')->nullable()->after('source');
            $table->unsignedBigInteger('branch_id')->nullable()->after('cashier_id');
            $table->unsignedBigInteger('pos_session_id')->nullable()->after('branch_id');
            $table->boolean('is_hold')->default(false)->after('pos_session_id');
            $table->string('hold_reason', 255)->nullable()->after('is_hold');
            $table->string('order_note', 500)->nullable()->after('hold_reason');
            $table->date('requested_delivery_date')->nullable()->after('order_note');
            $table->string('delivery_time_slot', 50)->nullable()->after('requested_delivery_date');

            $table->index(['source', 'created_at']);
            $table->index('cashier_id');
            $table->index('is_hold');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'source', 'cashier_id', 'branch_id', 'pos_session_id',
                'is_hold', 'hold_reason', 'order_note',
                'requested_delivery_date', 'delivery_time_slot',
            ]);
        });
    }
};

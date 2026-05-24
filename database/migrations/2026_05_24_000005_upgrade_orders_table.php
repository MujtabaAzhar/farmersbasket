<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_status')->default('pending')->after('status');
            $table->string('tracking_number', 100)->nullable()->after('payment_status');
            $table->string('courier_name', 100)->nullable()->after('tracking_number');
            $table->date('estimated_delivery_date')->nullable()->after('courier_name');
            $table->string('coupon_code', 50)->nullable()->after('estimated_delivery_date');
        });

        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('ordered','confirmed','packed','shipped','delivered','canceled','returned') NOT NULL DEFAULT 'ordered'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('ordered','delivered','canceled') NOT NULL DEFAULT 'ordered'");

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'tracking_number', 'courier_name', 'estimated_delivery_date', 'coupon_code']);
        });
    }
};

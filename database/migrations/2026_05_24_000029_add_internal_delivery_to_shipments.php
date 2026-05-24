<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->foreignId('rider_id')->nullable()->after('booked_by')
                  ->constrained('riders')->nullOnDelete();
            $table->enum('vehicle_type', ['bike', 'van', 'pickup'])->nullable()->after('rider_id');
            $table->string('delivery_time_slot', 50)->nullable()->after('estimated_delivery');
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropForeign(['rider_id']);
            $table->dropColumn(['rider_id', 'vehicle_type', 'delivery_time_slot']);
        });
    }
};

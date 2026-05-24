<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('courier_service_id')->constrained();
            $table->string('tracking_number')->nullable()->unique();
            $table->string('cn_number')->nullable()->comment("Courier's own consignment number");

            $table->enum('status', [
                'pending', 'booked', 'picked_up', 'in_transit',
                'out_for_delivery', 'delivered', 'failed', 'returned', 'canceled',
            ])->default('pending');

            // Recipient (copied from order at booking time)
            $table->string('recipient_name');
            $table->string('recipient_phone', 20);
            $table->string('recipient_address');
            $table->string('origin_city', 100)->default('Multan');
            $table->string('destination_city', 100);

            // Parcel details
            $table->decimal('weight', 8, 2)->default(0.5)->comment('KG');
            $table->unsignedSmallInteger('pieces')->default(1);
            $table->decimal('declared_value', 10, 2)->default(0);
            $table->text('special_instructions')->nullable();

            // Dates
            $table->date('booking_date')->nullable();
            $table->date('estimated_delivery')->nullable();
            $table->date('actual_delivery')->nullable();
            $table->timestamp('last_tracked_at')->nullable();

            // API response storage
            $table->json('raw_response')->nullable();

            $table->foreignId('booked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'status']);
            $table->index('tracking_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};

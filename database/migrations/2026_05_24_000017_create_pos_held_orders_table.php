<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Stores parked (held) POS carts so cashiers can service multiple customers.
// Cart contents are JSON-serialised. A held order has no matching orders row
// until the cashier resumes and completes it.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_held_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cashier_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->json('cart_data');
            $table->json('customer_data')->nullable();
            $table->json('gift_data')->nullable();
            $table->string('note', 255)->nullable();
            $table->timestamps();

            $table->index('cashier_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_held_orders');
    }
};

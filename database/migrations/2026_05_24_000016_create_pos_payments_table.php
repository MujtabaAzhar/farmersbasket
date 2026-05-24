<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// POS payments are tracked separately from the ecommerce 'transections' table
// because POS supports split tender (cash + card) and needs cash-change tracking.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->enum('method', ['cash', 'card', 'bank_transfer', 'split'])->default('cash');
            $table->decimal('amount', 10, 2);
            $table->decimal('cash_received', 10, 2)->nullable();
            $table->decimal('change_given', 10, 2)->nullable();
            $table->string('reference_no', 100)->nullable();
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_payments');
    }
};

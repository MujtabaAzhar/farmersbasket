<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_size_id')->nullable()->constrained('product_sizes')->nullOnDelete();
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->enum('type', ['order', 'cancel', 'adjustment', 'transfer_in', 'transfer_out', 'restock']);
            $table->unsignedInteger('quantity_before');
            $table->integer('quantity_change');
            $table->unsignedInteger('quantity_after');
            $table->string('note', 255)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['product_id', 'created_at']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_logs');
    }
};

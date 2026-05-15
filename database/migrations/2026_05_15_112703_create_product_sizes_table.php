<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_sizes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id')->unsigned();
            $table->string('size_label'); // e.g., "5 KG", "7 KG"
            $table->decimal('size_value', 8, 2); // e.g., 5, 7, 9
            $table->string('unit'); // e.g., "KG", "LB"
            $table->unsignedInteger('quantity')->default(0);
            $table->timestamps();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_sizes');
    }
};

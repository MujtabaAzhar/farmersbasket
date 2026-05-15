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
        Schema::table('product_sizes', function (Blueprint $table) {
            $table->decimal('regular_price', 8, 2)->default(0)->after('quantity');
            $table->decimal('sale_price', 8, 2)->nullable()->after('regular_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_sizes', function (Blueprint $table) {
            //
        });
    }
};

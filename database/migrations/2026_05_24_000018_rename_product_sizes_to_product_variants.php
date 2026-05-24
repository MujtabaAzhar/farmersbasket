<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Rename the table. MySQL InnoDB automatically updates FK references
        // in inventory_logs, warehouse_inventories, and stock_transfers.
        DB::statement('RENAME TABLE product_sizes TO product_variants');

        // Step 2: Add new columns
        Schema::table('product_variants', function (Blueprint $table) {
            $table->string('sku', 100)->unique()->nullable()->after('unit');
            $table->string('barcode', 100)->nullable()->after('sku');
            $table->decimal('cost_price', 10, 2)->nullable()->after('regular_price');
            $table->unsignedInteger('low_stock_alert')->default(5)->after('quantity');
            $table->boolean('is_active')->default(true)->after('low_stock_alert');
        });

        // Step 3: Rename columns to match new naming convention
        // size_label → variant_name, size_value → weight,
        // quantity → stock_qty, regular_price → price, sale_price → compare_price
        Schema::table('product_variants', function (Blueprint $table) {
            $table->renameColumn('size_label', 'variant_name');
            $table->renameColumn('size_value', 'weight');
            $table->renameColumn('quantity', 'stock_qty');
            $table->renameColumn('regular_price', 'price');
            $table->renameColumn('sale_price', 'compare_price');
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->renameColumn('variant_name', 'size_label');
            $table->renameColumn('weight', 'size_value');
            $table->renameColumn('stock_qty', 'quantity');
            $table->renameColumn('price', 'regular_price');
            $table->renameColumn('compare_price', 'sale_price');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['sku', 'barcode', 'cost_price', 'low_stock_alert', 'is_active']);
        });

        DB::statement('RENAME TABLE product_variants TO product_sizes');
    }
};

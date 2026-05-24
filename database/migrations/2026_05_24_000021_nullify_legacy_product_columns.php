<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // Products now get pricing and stock from product_variants.
    // Make the old columns nullable so product_store no longer requires them.
    public function up(): void
    {
        DB::statement("ALTER TABLE products MODIFY COLUMN regular_price DECIMAL(10,2) NULL DEFAULT NULL");
        DB::statement("ALTER TABLE products MODIFY COLUMN sale_price DECIMAL(10,2) NULL DEFAULT NULL");
        DB::statement("ALTER TABLE products MODIFY COLUMN sku VARCHAR(191) NULL DEFAULT NULL");
        // stock_status stays — computed from variants and stored for fast queries
        DB::statement("ALTER TABLE products MODIFY COLUMN stock_status ENUM('instock','outofstock') NOT NULL DEFAULT 'instock'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE products MODIFY COLUMN regular_price DECIMAL(10,2) NOT NULL");
        DB::statement("ALTER TABLE products MODIFY COLUMN sale_price DECIMAL(10,2) NULL");
        DB::statement("ALTER TABLE products MODIFY COLUMN sku VARCHAR(191) NOT NULL");
        DB::statement("ALTER TABLE products MODIFY COLUMN stock_status ENUM('instock','outofstock') NOT NULL");
    }
};

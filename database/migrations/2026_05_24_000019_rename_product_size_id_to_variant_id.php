<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── inventory_logs ────────────────────────────────────────────────────
        Schema::table('inventory_logs', function (Blueprint $table) {
            $table->dropForeign(['product_size_id']);
        });
        Schema::table('inventory_logs', function (Blueprint $table) {
            $table->renameColumn('product_size_id', 'variant_id');
        });
        Schema::table('inventory_logs', function (Blueprint $table) {
            $table->foreign('variant_id')->references('id')->on('product_variants')->nullOnDelete();
        });

        // ── warehouse_inventories ─────────────────────────────────────────────
        // Must drop the composite unique key that includes this column first.
        Schema::table('warehouse_inventories', function (Blueprint $table) {
            $table->dropForeign(['product_size_id']);
            $table->dropUnique('wh_inv_unique');
        });
        Schema::table('warehouse_inventories', function (Blueprint $table) {
            $table->renameColumn('product_size_id', 'variant_id');
        });
        Schema::table('warehouse_inventories', function (Blueprint $table) {
            $table->foreign('variant_id')->references('id')->on('product_variants')->nullOnDelete();
            $table->unique(['warehouse_id', 'product_id', 'variant_id'], 'wh_inv_unique');
        });

        // ── stock_transfers ───────────────────────────────────────────────────
        Schema::table('stock_transfers', function (Blueprint $table) {
            $table->dropForeign(['product_size_id']);
        });
        Schema::table('stock_transfers', function (Blueprint $table) {
            $table->renameColumn('product_size_id', 'variant_id');
        });
        Schema::table('stock_transfers', function (Blueprint $table) {
            $table->foreign('variant_id')->references('id')->on('product_variants')->nullOnDelete();
        });
    }

    public function down(): void
    {
        foreach (['inventory_logs', 'stock_transfers'] as $tbl) {
            Schema::table($tbl, function (Blueprint $table) {
                $table->dropForeign(['variant_id']);
            });
            Schema::table($tbl, function (Blueprint $table) {
                $table->renameColumn('variant_id', 'product_size_id');
            });
            Schema::table($tbl, function (Blueprint $table) use ($tbl) {
                $table->foreign('product_size_id')->references('id')->on('product_variants')->nullOnDelete();
            });
        }

        Schema::table('warehouse_inventories', function (Blueprint $table) {
            $table->dropForeign(['variant_id']);
            $table->dropUnique('wh_inv_unique');
        });
        Schema::table('warehouse_inventories', function (Blueprint $table) {
            $table->renameColumn('variant_id', 'product_size_id');
        });
        Schema::table('warehouse_inventories', function (Blueprint $table) {
            $table->foreign('product_size_id')->references('id')->on('product_variants')->nullOnDelete();
            $table->unique(['warehouse_id', 'product_id', 'product_size_id'], 'wh_inv_unique');
        });
    }
};

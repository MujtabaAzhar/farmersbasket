<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL auto-creates indexes on FK columns, so these are wrapped in try/catch
        // to be safe on both fresh installs and existing databases.

        $this->safeAddIndex('orders', function (Blueprint $table) {
            $table->index('user_id', 'orders_user_id_index');
        });

        $this->safeAddIndex('orders', function (Blueprint $table) {
            $table->index('status', 'orders_status_index');
        });

        $this->safeAddIndex('orders', function (Blueprint $table) {
            $table->index('created_at', 'orders_created_at_index');
        });

        $this->safeAddIndex('order_items', function (Blueprint $table) {
            $table->index('product_id', 'order_items_product_id_index');
        });

        $this->safeAddIndex('products', function (Blueprint $table) {
            $table->index('category_id', 'products_category_id_index');
        });

        $this->safeAddIndex('products', function (Blueprint $table) {
            $table->index('brand_id', 'products_brand_id_index');
        });

        $this->safeAddIndex('addresses', function (Blueprint $table) {
            $table->index('user_id', 'addresses_user_id_index');
        });

        $this->safeAddIndex('transections', function (Blueprint $table) {
            $table->index('order_id', 'transections_order_id_index');
        });

        $this->safeAddIndex('transections', function (Blueprint $table) {
            $table->index('user_id', 'transections_user_id_index');
        });

        // coupons.code already has a UNIQUE constraint (which implies an index).
        // coupons.expiry_date has no index — frequently used in WHERE clauses for validation.
        $this->safeAddIndex('coupons', function (Blueprint $table) {
            $table->index('expiry_date', 'coupons_expiry_date_index');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndexIfExists('orders_user_id_index');
            $table->dropIndexIfExists('orders_status_index');
            $table->dropIndexIfExists('orders_created_at_index');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndexIfExists('order_items_product_id_index');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndexIfExists('products_category_id_index');
            $table->dropIndexIfExists('products_brand_id_index');
        });

        Schema::table('addresses', function (Blueprint $table) {
            $table->dropIndexIfExists('addresses_user_id_index');
        });

        Schema::table('transections', function (Blueprint $table) {
            $table->dropIndexIfExists('transections_order_id_index');
        });

        Schema::table('transections', function (Blueprint $table) {
            $table->dropIndexIfExists('transections_user_id_index');
        });

        Schema::table('coupons', function (Blueprint $table) {
            $table->dropIndexIfExists('coupons_expiry_date_index');
        });
    }

    private function safeAddIndex(string $table, callable $callback): void
    {
        try {
            Schema::table($table, $callback);
        } catch (\Exception $e) {
            // Index already exists (FK-backed columns are auto-indexed by MySQL)
        }
    }
};

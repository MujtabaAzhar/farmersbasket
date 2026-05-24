<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $table = 'product_variants';

    protected $fillable = [
        'product_id', 'variant_name', 'weight', 'unit',
        'sku', 'barcode', 'price', 'compare_price', 'cost_price',
        'stock_qty', 'low_stock_alert', 'is_active',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'price'       => 'decimal:2',
        'compare_price' => 'decimal:2',
        'cost_price'  => 'decimal:2',
        'weight'      => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function inventoryLogs()
    {
        return $this->hasMany(InventoryLog::class, 'variant_id')->orderByDesc('created_at');
    }

    public function warehouseInventories()
    {
        return $this->hasMany(WarehouseInventory::class, 'variant_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'variant_id');
    }

    public function isLowStock(): bool
    {
        return $this->stock_qty > 0 && $this->stock_qty <= $this->low_stock_alert;
    }

    public function isInStock(): bool
    {
        return $this->is_active && $this->stock_qty > 0;
    }

    /** Human-readable label e.g. "1 KG Box" or "Jumbo (3.5 KG)" */
    public function getDisplayLabelAttribute(): string
    {
        if ($this->weight && $this->unit) {
            return $this->variant_name . ' (' . rtrim(rtrim(number_format((float)$this->weight, 2), '0'), '.') . ' ' . $this->unit . ')';
        }
        return $this->variant_name;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    protected $fillable = [
        'product_id', 'variant_id', 'warehouse_id',
        'type', 'quantity_before', 'quantity_change', 'quantity_after',
        'note', 'created_by',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function record(
        int $productId,
        string $type,
        int $quantityBefore,
        int $quantityChange,
        ?int $variantId = null,
        ?int $warehouseId = null,
        ?string $note = null,
        ?int $createdBy = null
    ): self {
        return self::create([
            'product_id'      => $productId,
            'variant_id'      => $variantId,
            'warehouse_id'    => $warehouseId,
            'type'            => $type,
            'quantity_before' => $quantityBefore,
            'quantity_change' => $quantityChange,
            'quantity_after'  => max(0, $quantityBefore + $quantityChange),
            'note'            => $note,
            'created_by'      => $createdBy,
        ]);
    }
}

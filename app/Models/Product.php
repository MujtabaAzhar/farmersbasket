<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'short_description',
        'description',
        'featured',
        'image',
        'images',
        'category_id',
        'brand_id',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class)->orderBy('weight');
    }

    /** Alias kept for backward compat with any old code referencing sizes() */
    public function sizes()
    {
        return $this->variants();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('status', 'approved');
    }

    public function wishlistItems()
    {
        return $this->hasMany(WishlistItem::class);
    }

    public function inventoryLogs()
    {
        return $this->hasMany(InventoryLog::class)->orderByDesc('created_at');
    }

    public function warehouseInventories()
    {
        return $this->hasMany(WarehouseInventory::class);
    }

    // ── Computed helpers ──────────────────────────────────────────────────────

    public function averageRating(): float
    {
        return round($this->approvedReviews()->avg('rating') ?? 0, 1);
    }

    /** Lowest active variant price. Returns null if no variants. */
    public function getMinPriceAttribute(): ?float
    {
        if ($this->relationLoaded('variants')) {
            $prices = $this->variants->where('is_active', true)->pluck('price');
            return $prices->isNotEmpty() ? (float) $prices->min() : null;
        }
        return (float) ProductVariant::where('product_id', $this->id)
            ->where('is_active', true)->min('price');
    }

    /** Returns true if any active variant has stock. */
    public function inStock(): bool
    {
        if ($this->relationLoaded('variants')) {
            return $this->variants->where('is_active', true)->where('stock_qty', '>', 0)->isNotEmpty();
        }
        return ProductVariant::where('product_id', $this->id)
            ->where('is_active', true)->where('stock_qty', '>', 0)->exists();
    }

    /** Total stock across all variants (used for legacy inventory queries). */
    public function getTotalStockAttribute(): int
    {
        if ($this->relationLoaded('variants')) {
            return (int) $this->variants->sum('stock_qty');
        }
        return (int) ProductVariant::where('product_id', $this->id)->sum('stock_qty');
    }

    /** Update products.stock_status based on variant stock. Call after stock changes. */
    public function syncStockStatus(): void
    {
        $this->stock_status = $this->inStock() ? 'instock' : 'outofstock';
        $this->save();
    }

    public function isLowStock(int $threshold = 10): bool
    {
        return $this->total_stock > 0 && $this->total_stock <= $threshold;
    }
}

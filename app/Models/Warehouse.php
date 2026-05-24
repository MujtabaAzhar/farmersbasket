<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = [
        'name', 'code', 'address', 'city',
        'manager_name', 'manager_phone', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function inventories()
    {
        return $this->hasMany(WarehouseInventory::class);
    }

    public function outboundTransfers()
    {
        return $this->hasMany(StockTransfer::class, 'from_warehouse_id');
    }

    public function inboundTransfers()
    {
        return $this->hasMany(StockTransfer::class, 'to_warehouse_id');
    }

    public function totalStock(): int
    {
        return (int) $this->inventories()->sum('quantity');
    }
}

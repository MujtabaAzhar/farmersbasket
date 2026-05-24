<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rider extends Model
{
    protected $fillable = ['name', 'phone', 'vehicle_type', 'branch_id', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    const VEHICLES = ['bike' => 'Bike', 'van' => 'Van', 'pickup' => 'Pickup Truck'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    public function getVehicleLabelAttribute(): string
    {
        return self::VEHICLES[$this->vehicle_type] ?? ucfirst($this->vehicle_type);
    }
}

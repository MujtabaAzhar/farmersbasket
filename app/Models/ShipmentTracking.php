<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentTracking extends Model
{
    protected $fillable = [
        'shipment_id', 'status', 'description', 'location', 'event_time', 'source', 'raw_data',
    ];

    protected $casts = [
        'event_time' => 'datetime',
        'raw_data'   => 'array',
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public static function record(int $shipmentId, string $status, string $description, ?string $location = null, ?string $eventTime = null, string $source = 'manual'): self
    {
        return self::create([
            'shipment_id' => $shipmentId,
            'status'      => $status,
            'description' => $description,
            'location'    => $location,
            'event_time'  => $eventTime ?? now(),
            'source'      => $source,
        ]);
    }
}

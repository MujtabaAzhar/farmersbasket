<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $fillable = [
        'order_id', 'courier_service_id', 'tracking_number', 'cn_number', 'status',
        'recipient_name', 'recipient_phone', 'recipient_address',
        'origin_city', 'destination_city',
        'weight', 'pieces', 'declared_value', 'special_instructions',
        'booking_date', 'estimated_delivery', 'actual_delivery', 'last_tracked_at',
        'delivery_time_slot', 'rider_id', 'vehicle_type',
        'raw_response', 'booked_by', 'notes',
    ];

    protected $casts = [
        'raw_response'       => 'array',
        'booking_date'       => 'date',
        'estimated_delivery' => 'date',
        'actual_delivery'    => 'date',
        'last_tracked_at'    => 'datetime',
        'weight'             => 'decimal:2',
        'declared_value'     => 'decimal:2',
    ];

    const STATUSES = [
        'pending'          => ['label' => 'Pending',            'color' => 'secondary'],
        'booked'           => ['label' => 'Pending Pickup',     'color' => 'info'],
        'picked_up'        => ['label' => 'Picked Up',          'color' => 'primary'],
        'in_transit'       => ['label' => 'In Transit',         'color' => 'warning'],
        'out_for_delivery' => ['label' => 'Out for Delivery',   'color' => 'orange'],
        'delivered'        => ['label' => 'Delivered',          'color' => 'success'],
        'failed'           => ['label' => 'Failed Delivery',    'color' => 'danger'],
        'returned'         => ['label' => 'Returned',           'color' => 'dark'],
        'canceled'         => ['label' => 'Canceled',           'color' => 'danger'],
    ];

    const TIME_SLOTS = [
        '10am-1pm'  => '10:00 AM – 1:00 PM',
        '1pm-4pm'   => '1:00 PM – 4:00 PM',
        '4pm-7pm'   => '4:00 PM – 7:00 PM',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function courier()
    {
        return $this->belongsTo(CourierService::class, 'courier_service_id');
    }

    public function rider()
    {
        return $this->belongsTo(Rider::class);
    }

    public function trackings()
    {
        return $this->hasMany(ShipmentTracking::class)->orderBy('event_time', 'desc');
    }

    public function bookedBy()
    {
        return $this->belongsTo(User::class, 'booked_by');
    }

    public function isInternal(): bool
    {
        return $this->courier?->code === 'internal';
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status]['label'] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUSES[$this->status]['color'] ?? 'secondary';
    }

    public function getTimeSlotLabelAttribute(): ?string
    {
        return self::TIME_SLOTS[$this->delivery_time_slot] ?? $this->delivery_time_slot;
    }

    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }

    public function progressPercent(): int
    {
        return match ($this->status) {
            'pending'          => 0,
            'booked'           => 15,
            'picked_up'        => 35,
            'in_transit'       => 60,
            'out_for_delivery' => 80,
            'delivered'        => 100,
            default            => 0,
        };
    }

    /** Whether a given status key appears in this shipment's tracking history. */
    public function hasTrackingStatus(string $status): bool
    {
        return $this->trackings->contains('status', $status);
    }
}

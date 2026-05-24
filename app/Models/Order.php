<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subtotal',
        'discount',
        'tax',
        'total',
        'name',
        'phone',
        'locality',
        'address',
        'city',
        'state',
        'country',
        'landmark',
        'zip',
        'type',
        'status',
        'is_shipping_different',
        'delivered_date',
        'canceled_date',
        'payment_status',
        'tracking_number',
        'courier_name',
        'estimated_delivery_date',
        'coupon_code',
        'source',
        'cashier_id',
        'branch_id',
        'pos_session_id',
        'is_hold',
        'hold_reason',
        'order_note',
        'requested_delivery_date',
        'delivery_time_slot',
    ];

    public function getOrderNumberAttribute(): string
    {
        return 'FB-' . (1000 + $this->id);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function transaction()
    {
        return $this->hasOne(Transection::class);
    }

    public function giftOrder()
    {
        return $this->hasOne(GiftOrder::class);
    }

    public function histories()
    {
        return $this->hasMany(OrderHistory::class)->orderBy('created_at', 'asc');
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function posSession()
    {
        return $this->belongsTo(PosSession::class, 'pos_session_id');
    }

    public function posPayment()
    {
        return $this->hasOne(PosPayment::class);
    }
}

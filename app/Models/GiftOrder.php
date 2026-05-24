<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GiftOrder extends Model
{
    protected $fillable = [
        'order_id',
        'sender_name',
        'sender_phone',
        'sender_email',
        'sender_address',
        'sender_city',
        'receiver_name',
        'receiver_phone',
        'receiver_city',
        'receiver_address',
        'gift_message',
        'gift_wrapping',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

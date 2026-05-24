<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosPayment extends Model
{
    protected $fillable = [
        'order_id', 'method', 'amount',
        'cash_received', 'change_given', 'reference_no',
        'online_platform', 'payment_verified', 'notes',
    ];

    protected $casts = [
        'amount'           => 'decimal:2',
        'cash_received'    => 'decimal:2',
        'change_given'     => 'decimal:2',
        'payment_verified' => 'boolean',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

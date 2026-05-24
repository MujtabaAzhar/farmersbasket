<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosHeldOrder extends Model
{
    protected $fillable = ['cashier_id', 'branch_id', 'cart_data', 'customer_data', 'gift_data', 'note'];

    protected $casts = [
        'cart_data'     => 'array',
        'customer_data' => 'array',
        'gift_data'     => 'array',
    ];

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}

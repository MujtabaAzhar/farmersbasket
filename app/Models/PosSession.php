<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosSession extends Model
{
    protected $fillable = [
        'user_id', 'branch_id', 'opening_balance',
        'closing_balance', 'expected_cash', 'status', 'notes',
        'opened_at', 'closed_at',
    ];

    protected $casts = [
        'opened_at'  => 'datetime',
        'closed_at'  => 'datetime',
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'expected_cash'   => 'decimal:2',
    ];

    public function cashier()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'pos_session_id');
    }

    public function totalSales(): float
    {
        return (float) $this->orders()->where('is_hold', false)->sum('total');
    }

    public function orderCount(): int
    {
        return $this->orders()->where('is_hold', false)->count();
    }
}

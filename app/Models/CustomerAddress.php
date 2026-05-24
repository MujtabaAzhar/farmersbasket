<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    protected $fillable = ['customer_id', 'title', 'address', 'city', 'is_default'];

    protected $casts = ['is_default' => 'boolean'];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}

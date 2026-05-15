<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSize extends Model
{
    protected $fillable = ['product_id', 'size_label', 'size_value', 'unit', 'quantity', 'regular_price', 'sale_price'];

    public function product(){
        return $this->belongsTo(Product::class, 'product_id');
    }
}

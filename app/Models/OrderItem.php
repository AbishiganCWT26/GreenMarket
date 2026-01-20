<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'product_id', 'product_name_snapshot', 'quantity_ordered',
        'unit_price_snapshot', 'item_total'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Add this relationship
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

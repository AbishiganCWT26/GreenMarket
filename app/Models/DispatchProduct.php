<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispatchProduct extends Model
{
    protected $fillable = [
        'bus_dispatch_id',
        'order_item_id'
    ];

    public function busDispatch()
    {
        return $this->belongsTo(BusDispatch::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
}

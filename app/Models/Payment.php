<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id', 'payment_reference', 'amount', 'payment_method',
        'payment_status', 'payment_date', 'transaction_id', 'receipt_url'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

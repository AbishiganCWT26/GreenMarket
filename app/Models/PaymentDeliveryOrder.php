<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentDeliveryOrder extends Model
{
    protected $table = 'payment_delivery_order';
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'transaction_id',
        'transaction_date',
        'transaction_time',
        'payment_slip_path',
        'payment_status',
        'rejection_reason',
        'resubmission_count',
        'last_resubmitted_at'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

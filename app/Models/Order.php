<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'buyer_id', 'farmer_id', 'lead_farmer_id',
        'order_status', 'total_amount', 'order_date', 'paid_date', 'completed_date'
    ];

    public function buyer()
    {
        return $this->belongsTo(Buyer::class);
    }

    public function farmer()
    {
        return $this->belongsTo(Farmer::class);
    }

    public function leadFarmer()
    {
        return $this->belongsTo(LeadFarmer::class);
    }

    // Change from items() to orderItems()
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Keep items() as an alias if needed
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}

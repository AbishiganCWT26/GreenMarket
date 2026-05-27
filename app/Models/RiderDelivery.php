<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiderDelivery extends Model
{
    protected $fillable = [
        'bus_dispatch_id',
        'rider_id',
        'order_id',
        'delivery_status',
        'claimed_by_rider_id',
        'claimed_at',
        'acceptance_window_closed',
        'admin_assigned_rider_id',
        'assigned_by_admin_at',
        'escalation_level',
        'pickup_confirmed_at',
    ];

    protected $casts = [
        'claimed_at'           => 'datetime',
        'assigned_by_admin_at' => 'datetime',
        'pickup_confirmed_at'  => 'datetime',
    ];

    public function busDispatch()
    {
        return $this->belongsTo(BusDispatch::class);
    }

    public function rider()
    {
        return $this->belongsTo(User::class, 'rider_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

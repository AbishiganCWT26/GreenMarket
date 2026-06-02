<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusDispatch extends Model
{
    protected $fillable = [
        'bus_number',
        'bus_image',
        'conductor_mobile',
        'conductor_name',
        'estimated_arrival_time',
        'dispatch_status',
        'lead_farmer_id'
    ];

    public function leadFarmer()
    {
        return $this->belongsTo(User::class, 'lead_farmer_id');
    }

    public function dispatchProducts()
    {
        return $this->hasMany(DispatchProduct::class);
    }
}

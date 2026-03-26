<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Farmer extends Model
{
    protected $table = 'farmers';

    protected $fillable = [
        'user_id', 'lead_farmer_id', 'name', 'nic_no', 'primary_mobile',
        'whatsapp_number', 'email', 'residential_address', 'address_map_link',
        'preferred_payment', 'payment_details', 'grama_niladhari_division', 'gn_division_code', 'is_active',
        'district', 'divisional_secretariat', 'bank_name', 'bank_branch', 'account_holder_name', 'account_number',
        'ezcash_mobile', 'mcash_mobile', 'updated_by'
    ];

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leadFarmer()
    {
        return $this->belongsTo(LeadFarmer::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}

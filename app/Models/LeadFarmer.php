<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadFarmer extends Model
{
    use HasFactory;

    protected $table = 'lead_farmers';

    protected $fillable = [
        'user_id',
        'name',
        'nic_no',
        'primary_mobile',
        'whatsapp_number',
        'residential_address',
        'grama_niladhari_division',
        'gn_division_code',
        'district',
        'divisional_secretariat',
        'group_name',
        'group_number',
        'preferred_payment',
        'payment_details',
        'account_number',
        'account_holder_name',
        'bank_name',
        'bank_branch',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user associated with the lead farmer.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the farmers managed by this lead farmer.
     */
    public function farmers()
    {
        return $this->hasMany(Farmer::class);
    }

    /**
     * Get the products listed by this lead farmer.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the orders for this lead farmer.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}

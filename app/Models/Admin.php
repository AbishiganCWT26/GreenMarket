<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'nic_no',
        'role',
        'phone_number',
        'zone_assigned_area'
    ];

    protected $attributes = [
        'nic_no' => 'NOT_SET',
        'role' => 'admin',
        'zone_assigned_area' => 'Sri Lanka'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facilitator extends Model
{
    protected $table = 'facilitators';

    protected $fillable = [
        'user_id', 'name', 'nic_no', 'primary_mobile', 'whatsapp_number',
        'email', 'assigned_division', 'is_active'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

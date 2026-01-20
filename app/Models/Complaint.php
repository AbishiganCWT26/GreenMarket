<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'complainant_user_id',
        'complainant_role',
        'against_user_id',
        'related_order_id',
        'complaint_type',
        'description',
        'status',
        'resolved_by_facilitator_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function complainant()
    {
        return $this->belongsTo(User::class, 'complainant_user_id');
    }

    public function againstUser()
    {
        return $this->belongsTo(User::class, 'against_user_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'related_order_id');
    }

    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolved_by_facilitator_id');
    }
}

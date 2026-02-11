<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id', 'recipient_type', 'recipient_address',
        'title', 'message', 'notification_type', 'is_read', 'related_id'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Ensure timestamps are enabled
    public $timestamps = true;

    // Explicit date attributes
    protected $dates = ['created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessor to ensure created_at is always a Carbon instance
    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value);
    }
}

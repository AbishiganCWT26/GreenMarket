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
        'zone_assigned_area',
        'updated_by'
    ];

    protected $attributes = [
        'nic_no' => 'NOT_SET',
        'role' => 'admin',
        'zone_assigned_area' => 'Sri Lanka'
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

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}

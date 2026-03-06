<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    use HasFactory;

    protected $table = 'inventory_logs';

    protected $fillable = [
        'product_id',
        'user_id',
        'order_id',
        'quantity_change',
        'new_quantity',
        'type',
        'reason',
    ];

    /**
     * Get the product associated with the log.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who performed the action.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order associated with the log.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

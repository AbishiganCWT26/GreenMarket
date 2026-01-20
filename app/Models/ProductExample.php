<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductExample extends Model
{
    protected $fillable = [
        'subcategory_id',
        'product_name',
        'description',
        'is_active',
        'display_order'
    ];

    // Relationship with subcategory
    public function subcategory()
    {
        return $this->belongsTo(ProductSubcategory::class, 'subcategory_id');
    }
}

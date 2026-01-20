<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSubcategory extends Model
{
    protected $fillable = [
        'category_id',  // This is the correct column name
        'subcategory_name',
        'description',
        'is_active',
        'display_order'
    ];

    // Relationship with category
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    // Relationship with product examples
    public function productExamples()
    {
        return $this->hasMany(ProductExample::class, 'subcategory_id');
    }
}

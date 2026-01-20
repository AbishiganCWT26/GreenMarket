<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     * Explicitly defining this avoids issues with pluralization.
     */
    protected $table = 'product_categories';

    protected $fillable = [
        'category_name',
        'description',
        'icon_filename',
        'is_active',
        'display_order',
        'created_by_user_id'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    /**
     * Relationship with subcategories.
     * A category can have many subcategories (e.g., Vegetables -> Leafy Greens).
     */
    public function subcategories()
    {
        return $this->hasMany(ProductSubcategory::class, 'category_id');
    }

    /**
     * Relationship with products.
     * Allows you to fetch all products directly under this category.
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}

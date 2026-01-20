<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'farmer_id',
        'lead_farmer_id',
        'product_name',
        'product_description',
        'product_photo',
        'type_variant',
        'category_id',
        'subcategory_id',
        'quantity',
        'unit_of_measure',
        'quality_grade',
        'expected_availability_date',
        'selling_price',
        'pickup_address',
        'pickup_map_link',
        'is_available',
        'views_count',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'is_available' => 'boolean',
        'expected_availability_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // --- Relationships ---

    /**
     * Get the farmer who owns the product.
     */
    public function farmer()
    {
        return $this->belongsTo(Farmer::class, 'farmer_id');
    }

    /**
     * Get the lead farmer who listed the product.
     */
    public function leadFarmer()
    {
        return $this->belongsTo(LeadFarmer::class, 'lead_farmer_id');
    }

    /**
     * Get the product category.
     */
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    /**
     * Get the product subcategory.
     */
    public function subcategory()
    {
        return $this->belongsTo(ProductSubcategory::class, 'subcategory_id');
    }

    /**
     * Get the order items for this product.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }

    // --- Accessors & Logic ---

    /**
     * Check if product is in stock.
     * Usage: $product->is_in_stock
     */
    public function getIsInStockAttribute()
    {
        return $this->quantity > 0 && $this->is_available;
    }

    /**
     * Format selling price with currency.
     * Usage: $product->formatted_price
     */
    public function getFormattedPriceAttribute()
    {
        return 'LKR ' . number_format($this->selling_price, 2);
    }
}

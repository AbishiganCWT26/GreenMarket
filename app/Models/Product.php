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
        'stock_capacity',
        'low_stock_threshold_percent',
        'unit_of_measure',
        'quality_grade',
        'expected_availability_date',
        'selling_price',
        'pickup_address',
        'pickup_map_link',
        'is_available',
        'views_count',
        'product_examples_id',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'stock_capacity' => 'decimal:2',
        'low_stock_threshold_percent' => 'decimal:2',
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
     * Get the specific product example.
     */
    public function productExample()
    {
        return $this->belongsTo(ProductExample::class, 'product_examples_id');
    }

    /**
     * Get the order items for this product.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }

    /**
     * Get the inventory logs for this product.
     */
    public function inventoryLogs()
    {
        return $this->hasMany(InventoryLog::class);
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

    /**
     * Get the inventory status of the product.
     * Usage: $product->inventory_status
     */
    public function getInventoryStatusAttribute()
    {
        if ($this->quantity <= 0) {
            return 'Out of Stock';
        }

        if ($this->stock_capacity > 0) {
            $percent = ($this->quantity / $this->stock_capacity) * 100;
            if ($percent <= 5) {
                return 'Critical';
            }
            if ($percent <= $this->low_stock_threshold_percent) {
                return 'Low Stock';
            }
        } elseif ($this->quantity < 5) {
            // Fallback if no capacity is set
            return 'Low Stock';
        }

        return 'In Stock';
    }
}

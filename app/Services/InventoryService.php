<?php

namespace App\Services;

use App\Models\Product;
use App\Models\InventoryLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Update product stock and log the movement.
     *
     * @param Product $product
     * @param float $quantityChange Positive for additions, negative for reductions
     * @param string $type manual_add, manual_reduce, order_placed, order_cancelled, etc.
     * @param string|null $reason
     * @param int|null $orderId
     * @return Product
     */
    public function updateStock(Product $product, $quantityChange, $type, $reason = null, $orderId = null)
    {
        return DB::transaction(function () use ($product, $quantityChange, $type, $reason, $orderId) {
            $oldQuantity = $product->quantity;
            $newQuantity = $oldQuantity + $quantityChange;

            // Ensure quantity doesn't go below zero
            $newQuantity = max(0, $newQuantity);

            $product->quantity = $newQuantity;
            
            // Auto-update availability based on stock
            if ($newQuantity <= 0) {
                $product->is_available = false;
            } elseif ($newQuantity > 0 && $oldQuantity <= 0) {
                // If it was out of stock and now has stock, we might want to keep it unavailable 
                // until the lead farmer manually enables it, but for simplicity let's auto-enable
                // if the product status allows it.
                if ($product->product_status !== 'removed by lead farmer') {
                    $product->is_available = true;
                }
            }

            $product->save();

            InventoryLog::create([
                'product_id' => $product->id,
                'user_id' => Auth::id() ?? 1, // Default to system user if no auth
                'order_id' => $orderId,
                'quantity_change' => $quantityChange,
                'new_quantity' => $newQuantity,
                'type' => $type,
                'reason' => $reason,
            ]);

            return $product;
        });
    }

    /**
     * Set product stock to a specific value and log as an adjustment.
     *
     * @param Product $product
     * @param float $newQuantity
     * @param string $reason
     * @return Product
     */
    public function adjustStock(Product $product, $newQuantity, $reason)
    {
        $quantityChange = $newQuantity - $product->quantity;
        return $this->updateStock($product, $quantityChange, 'manual_adjust', $reason);
    }
}

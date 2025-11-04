<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    protected $guarded = ['id'];

    /**
     * Relationship: Product has many OrderItems
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relationship: Product belongs to many Orders through OrderItems
     */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_items')
                    ->withPivot('quantity', 'unit_price', 'total_price', 'product_snapshot')
                    ->withTimestamps();
    }

    /**
     * Check if product is in stock
     */
    public function isInStock($quantity = 1): bool
    {
        return $this->stock >= $quantity;
    }

    /**
     * Reduce stock when order is placed
     */
    public function reduceStock(int $quantity): void
    {
        $this->decrement('stock', $quantity);
    }

    /**
     * Increase stock when order is cancelled/refunded
     */
    public function increaseStock(int $quantity): void
    {
        $this->increment('stock', $quantity);
    }
}

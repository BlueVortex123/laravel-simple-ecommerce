<?php

namespace App\Models;

use App\Models\User;
use App\Models\Product;
use App\Models\OrderItem;
use App\Enums\OrderStatusEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'shipping_address' => 'array',
        'billing_address' => 'array',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',

        'payment_method' => PaymentMethodEnum::class,
        'payment_status' => PaymentStatusEnum::class,
    ];

    /**
     * Generate unique order number
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($order) {
            if (!$order->order_number) {
                $order->order_number = 'ORD-' . now()->format('Y') . '-' . str_pad(
                    (Order::whereYear('created_at', now()->year)->count() + 1), 
                    6, '0', STR_PAD_LEFT
                );
            }
        });
    }

    /**
     * Relationship: Order belongs to a User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Order has many OrderItems
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relationship: Order has many status lines (history)
     */
    public function statusLines(): HasMany
    {
        return $this->hasMany(OrderStatusLine::class);
    }

    /**
     * Relationship: latest status line for this order (single row)
     */
    public function lastStatusLine(): HasOne
    {
        return $this->hasOne(OrderStatusLine::class)->latestOfMany();
    }

    /**
     * Denormalized relation: the last status as a lookup to `order_statuses`.
     */
    public function lastOrderStatus(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'last_order_status_id');
    }

    /**
     * Relationship: Order has many Products through OrderItems
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'order_items')
                    ->withPivot('quantity', 'unit_price', 'total_price', 'product_snapshot')
                    ->withTimestamps();
    }

    /**
     * Scope: Get orders by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Get recent orders
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Check if order can be cancelled
     */
    public function canBeCancelled(): bool
    {
        $status = $this->lastOrderStatus?->name ?? 'pending';
        return in_array($status, ['pending', 'processing']);
    }

    /**
     * Check if order is completed
     */
    public function isCompleted(): bool
    {
        return ($this->lastOrderStatus?->name ?? '') === 'delivered';
    }
}

<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Store a new order in the system.
     * 1. Validate stock availability for each item.
     * 2. Calculate total amount.
     * 3. Create Order and OrderItems models records.
     * 4. Update the stock for each product.
     */
    public function storeOrder(int $userId, array $items, array $orderData): Order
    {
        return DB::transaction(function () use ($userId, $items, $orderData) {
            $totalAmount = 0;
            $products = [];

            // First pass: validate stock and calculate total
            foreach ($items as $item) {
                $product = Product::find($item['product_id']);

                if (!$product) {
                    throw new \Exception("Product not found with ID: {$item['product_id']}");
                }

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product '{$product->name}'. Available: {$product->stock}, Requested: {$item['quantity']}");
                }

                $totalAmount += $product->price * $item['quantity'];
                $products[$item['product_id']] = $product;
            }

            // Create the order
            $order = Order::create([
                'user_id' => $userId,
                'total_amount' => $totalAmount,
                'shipping_address' => $orderData['shipping_address'],
                'billing_address' => $orderData['billing_address'] ?? null,
                'payment_method' => $orderData['payment_method'],
                'payment_status' => 'pending',
                'status' => 'pending',
                'notes' => $orderData['notes'] ?? null,
            ]);

            // Second pass: create order items and update stock
            foreach ($items as $item) {
                $product = $products[$item['product_id']];
                
                // Reduce stock
                $product->decrement('stock', $item['quantity']);

                // Create order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'total_price' => $product->price * $item['quantity'],
                    'product_snapshot' => [
                        'name' => $product->name,
                        'description' => $product->description,
                        'image' => $product->image,
                        'original_price' => $product->price,
                    ]
                ]);
            }

            return $order->load(['orderItems.product', 'user']);
        });
    }
}

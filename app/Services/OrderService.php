<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;

class OrderService
{
    // Store a new order in the system.
    // 1. Validate stock availability for each item.
    // 2. Calculate total amount.
    // 3. Create Order and OrderItems records.
    // 4. Reduce stock for each product.
    public function storeOrder(int $userId, array $items, array $orderData): void
    {

        $totalAmount = 0;
        $products = [];

        foreach ($items as $item) {
            $product = Product::find($item['product_id']);

            if (!$product || $product->stock < $item['quantity']) {
                throw new \Exception("Insufficient stock for product ID: {$item['product_id']}");
            }

            $totalAmount += $product->price * $item['quantity'];
            $products[$item['product_id']] = $product;
        }

        $order = Order::create([
            'user_id' => $userId,
            'total_amount' => $totalAmount,
            'shipping_address' => $orderData['shipping_address'],
            'billing_address' => $orderData['billing_address'],
            'payment_method' => $orderData['payment_method'],
            'payment_status' => 'pending',
        ]);

        foreach ($items as $item) {
            $product = $products[$item['product_id']];
            $product->stock -= $item['quantity'];
            $product->save();

            $order->orderItems()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $product->price,
                'total_price' => $product->price * $item['quantity'],
                'product_snapshot' => json_encode([
                    'name' => $product->name,
                    'description' => $product->description,
                    'image' => $product->image,
                    'original_price' => $product->price,
                ])
            ]);
        }
    }
}

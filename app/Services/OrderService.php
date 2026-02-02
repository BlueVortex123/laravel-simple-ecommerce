<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\OrderStatusLine;
use App\Models\OrderStatus;
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
                // `status` column removed; we will initialize last_order_status via status lines
                'notes' => $orderData['notes'] ?? null,
            ]);

            // Initialize order status: create an initial status line for 'pending' if available
            $pending = OrderStatus::where('name', 'pending')->first();
            if ($pending) {
                $this->addStatusLine($order, $pending->id);
            }

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

    /**
     * Add a status line for an order and update the denormalized last status on orders.
     * This is performed in a transaction to keep data consistent.
     *
     * @param  Order|int  $order
     * @param  int  $orderStatusId
     * @return OrderStatusLine
     */
    public function addStatusLine($order, int $orderStatusId): OrderStatusLine
    {
        return DB::transaction(function () use ($order, $orderStatusId) {
            $orderModel = $order instanceof Order ? $order : Order::findOrFail($order);

            $status = OrderStatus::findOrFail($orderStatusId);

            $line = OrderStatusLine::create([
                'order_id' => $orderModel->id,
                'order_status_id' => $orderStatusId,
            ]);

            // Update denormalized last status id and the orders.status enum/value
            $orderModel->last_order_status_id = $orderStatusId;
            // keep `status` in sync (store the canonical name)
            $orderModel->status = $status->name;
            $orderModel->save();

            return $line->load('status');
        });
    }
}

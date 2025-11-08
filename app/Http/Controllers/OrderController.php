<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // ! We will return only JSON responses from this controller for now.
    // * After finishing the backend, we will return Inertia views as needed, instead of JSON responses.

    /**
     * Display all listing of orders.
     * This method is used only by the admin.
     */
    public function allOrders()
    {
        // Check if the user is admin (handled by middleware)
        // If admin, retrieve all orders
        $orders = Order::lazy();
        return response()->json([
            'status' => 'success',
            'data' => $orders,
        ], 200);
    }

    /**
     * Display a listing of the current auth user's orders.
     * This method is used only by the authenticated user.
     */
    public function userOrders()
    {
        $orders = Order::where('user_id', auth()->id())->lazy();
        return response()->json([
            'status' => 'success',
            'data' => $orders,
        ], 200);
    }

    /**
     * Before placing an order, preview the order details.
     * This includes itemized costs and total amount.
     * This method is used by any user (auth or guest).
     */
    public function previewOrder()
    {
        request()->merge([
            'user_id' => auth()->id() ?? null,
            'items' => [
                [
                    'product_id' => 1,
                    'quantity' => 2,
                ],
                [
                    'product_id' => 3,
                    'quantity' => 1,
                ],
            ],
            'order_data' => [
                'shipping_address' => [
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'company' => 'Tech Corp',
                    'address_line_1' => '123 Main St',
                    'address_line_2' => 'Apt 4B',
                    'city' => 'New York',
                    'state' => 'NY',
                    'postal_code' => '10001',
                    'country' => 'United States',
                    'phone' => '+1-555-123-4567',
                ],
                'billing_address' => [
                    'first_name' => 'Jane',
                    'last_name' => 'Smith',
                    'company' => 'Business LLC',
                    'address_line_1' => '456 Oak Ave',
                    'address_line_2' => 'Suite 200',
                    'city' => 'Buffalo',
                    'state' => 'NY',
                    'postal_code' => '14201',
                    'country' => 'USA',
                ],
                'payment_method' => 'credit_card',
                'notes' => 'Please deliver between 9 AM and 5 PM.',
            ]
        ]);

        $items = collect(request()->items);
        $productIds = $items->pluck('product_id');

        $quantityMap = $items->pluck('quantity', 'product_id');
        $orderData = request()->order_data;

        dd(Product::whereIn('id', $productIds)
            ->select('id', 'name', 'price')
            ->get()
            // TBD use spatie dtos instead of map function.
            ->map(function ($product) use ($quantityMap, $orderData) {
                $quantity = $quantityMap[$product->id];
                return [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'unit_price' => $product->price,
                    'quantity' => $quantity,
                    'total_price' => $product->price * $quantity,
                    'shipping_address' => $orderData['shipping_address'],
                    'billing_address' => $orderData['billing_address'],
                    'payment_method' => $orderData['payment_method'],
                    'notes' => $orderData['notes'],
                ];
            }));
    }

    /**
     * Store a newly created order in storage.
     * This method is used by authenticated users.
     */
    public function placeOrder(?Request $request = null) // for now
    {
        request()->merge([
            'user_id' => auth()->id(),
            'items' => [
                [
                    "product_id" => 1,
                    "quantity" => 2,
                ],
                [
                    "product_id" => 3,
                    "quantity" => 1,
                ],
            ],
            "order_data" => [
                "shipping_address" =>  [
                    "first_name" => "John",
                    "last_name" => "Doe",
                    "company" => "Tech Corp",
                    "address_line_1" => "123 Main St",
                    "address_line_2" => "Apt 4B",
                    "city" => "New York",
                    "state" => "NY",
                    "postal_code" => "10001",
                    "country" => "United States",
                    "phone" => "+1-555-123-4567"
                ],
                "billing_address" => [
                    "first_name" => "Jane",
                    "last_name" => "Smith",
                    "company" => "Business LLC",
                    "address_line_1" => "456 Oak Ave",
                    "address_line_2" => "Suite 200",
                    "city" => "Buffalo",
                    "state" => "NY",
                    "postal_code" => "14201",
                    "country" => "USA"
                ],
                "payment_method" => "credit_card",
                "notes" => "Please deliver between 9 AM and 5 PM."
            ]
        ]);

        $validateData = request()->validate([
            'user_id' => 'required|exists:users,id',
            
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',

            'order_data' => 'required|array',
            'order_data.shipping_address' => 'required|array',
            'order_data.billing_address' => 'nullable|array',
            'order_data.payment_method' => 'required|string|max:255',
            'order_data.notes' => 'nullable|string|max:1000',
        ]);

        app(OrderService::class)->storeOrder($validateData['user_id'], $validateData['items'], $validateData['order_data']);
        return response()->json([
            'status' => 'success',
            'message' => 'Order placed successfully.',
        ], 201);
    }

    /**
     * Display the specified order.
     * This method is used only by the user who owns the order or by admin.
     */
    public function viewOrder(string $id)
    {
        //
    }

    /**
     * Cancel the specified order.
     * This method is used only by the user who owns the order or by admin.
     */
    public function cancelOrder(string $id)
    {
        //
    }
}

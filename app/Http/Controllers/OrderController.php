<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use App\Services\TestDataService;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

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
    public function userOrders(Request $request)
    {
        $perPage = 10;
        $search = $request->get('search', '');
        $page = $request->get('page', 1);
        $sortColumn = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        $query = Order::select('id', 'order_number', 'status', 'total_amount', 'payment_status', 'created_at')
            ->with('user:id,name,email');
            // ->where('user_id', Auth::id()); // Wip: admin should see all orders and normal users only their own

        // Apply search filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', '%' . $search . '%')
                  ->orWhere('status', 'like', '%' . $search . '%')
                  ->orWhere('payment_status', 'like', '%' . $search . '%')
                  ->orWhere('total_amount', 'like', '%' . $search . '%');
            });
        }

        // Apply sorting
        $query->orderBy($sortColumn, $sortDirection);

        $paginatedOrders = $query->paginate($perPage, ['*'], 'page', $page);

        // For initial page load, also send all orders for client-side pagination fallback
        $responseData = [
            'orders' => $paginatedOrders->items(),
            'pagination' => [
                'current_page' => $paginatedOrders->currentPage(),
                'last_page' => $paginatedOrders->lastPage(),
                'total' => $paginatedOrders->total(),
                'per_page' => $paginatedOrders->perPage(),
                'from' => $paginatedOrders->firstItem(),
                'to' => $paginatedOrders->lastItem(),
            ],
            'filters' => [
                'search' => $search,
                'sort' => $sortColumn,
                'direction' => $sortDirection,
            ]
        ];

        // On initial load (no AJAX request), also provide allOrders for fallback
        if (!$request->header('X-Inertia')) {
            $responseData['allOrders'] = Order::select('id', 'order_number', 'status', 'total_amount', 'payment_status', 'created_at')
                ->with('user:id,name,email')
                ->where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get()
                ->toArray();
        }

        return \Inertia\Inertia::render('Orders/Index', $responseData);
    }

    /**
     * Before placing an order, preview the order details.
     * This includes itemized costs and total amount.
     * This method is used by any user (auth or guest).
     */
    public function previewOrder()
    {
        // Use TestDataService for sample data (remove in production)
        $testData = TestDataService::getSampleOrderData();
        $testData['user_id'] = Auth::id() ?? null;
        request()->merge($testData);

        $items = collect(request()->items);
        $productIds = $items->pluck('product_id');

        $quantityMap = $items->pluck('quantity', 'product_id');
        $orderData = request()->order_data;

        $previewData = Product::whereIn('id', $productIds)
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
                ];
            });

        return response()->json([
            'status' => 'success',
            'message' => 'Order preview generated successfully.',
            'data' => [
                'items' => $previewData,
                'shipping_address' => $orderData['shipping_address'],
                'billing_address' => $orderData['billing_address'],
                'payment_method' => $orderData['payment_method'],
                'notes' => $orderData['notes'],
                'total_items' => $previewData->count(),
                'total_amount' => $previewData->sum('total_price'),
            ]
        ], 200);
    }

    /**
     * Store a newly created order in storage.
     * This method is used by authenticated users.
     */
    public function placeOrder(?Request $request = null) // for now
    {
        // Use TestDataService for sample data (remove in production)
        if (!$request || !$request->has('items')) {
            request()->merge(TestDataService::getSampleOrderData());
        }

        try {
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

            $order = app(OrderService::class)->storeOrder(
                $validateData['user_id'],
                $validateData['items'],
                $validateData['order_data']
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Order placed successfully.',
                'data' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'total_amount' => $order->total_amount,
                    'status' => $order->status,
                    'items_count' => $order->orderItems->count(),
                ]
            ], JsonResponse::HTTP_CREATED);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed. Some fields are invalid.',
                'errors' => $e->errors()
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to place order: ' . $e->getMessage()
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Display the specified order.
     * This method is used only by the user who owns the order or by admin.
     */
    public function viewOrder(string $id)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Authentication required.'
                ], JsonResponse::HTTP_UNAUTHORIZED);
            }
            
            $query = Order::with(['orderItems.product', 'user']);            
            
            // If user is admin, they can access any order
            if ($user->hasRole('admin')) {
                $order = $query->findOrFail($id);
            } else {
                // If not admin, user can only access their own orders
                $order = $query->where('user_id', $user->id)->findOrFail($id);
            }
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                    'total_amount' => $order->total_amount,
                    'shipping_address' => $order->shipping_address,
                    'billing_address' => $order->billing_address,
                    'payment_method' => $order->payment_method,
                    'notes' => $order->notes,
                    'created_at' => $order->created_at,
                    'shipped_at' => $order->shipped_at,
                    'delivered_at' => $order->delivered_at,
                    'customer' => [
                        'id' => $order->user->id,
                        'name' => $order->user->name,
                        'email' => $order->user->email,
                    ],
                    'order_items' => $order->orderItems->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'product_id' => $item->product_id,
                            'product_name' => $item->product->name ?? $item->product_snapshot['name'] ?? 'Unknown Product',
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price,
                            'total_price' => $item->total_price,
                            'product_snapshot' => $item->product_snapshot,
                        ];
                    }),
                    'summary' => [
                        'subtotal' => $order->orderItems->sum('total_price'),
                        'total_amount' => $order->total_amount,
                        'items_count' => $order->orderItems->count(),
                    ]
                ]
            ], JsonResponse::HTTP_OK);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found or access denied.'
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve order: ' . $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
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

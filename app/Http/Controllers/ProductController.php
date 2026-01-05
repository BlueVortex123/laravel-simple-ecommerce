<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

   public function index(Request $request): \Inertia\Response
    {
        $perPage = 10;
        $search = $request->get('search', '');
        $page = $request->get('page', 1);
        $sortColumn = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        $query = Product::select('id', 'name', 'description', 'price', 'stock', 'created_at');

        // Apply search filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhere('id', 'like', '%' . $search . '%')
                  ->orWhere('price', 'like', '%' . $search . '%');
            });
        }

        // Apply sorting
        $query->orderBy($sortColumn, $sortDirection);

        $paginatedProducts = $query->paginate($perPage, ['*'], 'page', $page);

        return Inertia::render('Products/Index', [
            'products' => $paginatedProducts->items(),
            'pagination' => [
                'current_page' => $paginatedProducts->currentPage(),
                'last_page' => $paginatedProducts->lastPage(),
                'total' => $paginatedProducts->total(),
                'per_page' => $paginatedProducts->perPage(),
                'from' => $paginatedProducts->firstItem(),
                'to' => $paginatedProducts->lastItem(),
            ],
            'filters' => [
                'search' => $search,
                'sort' => $sortColumn,
                'direction' => $sortDirection,
            ]
        ]);
    }

    /**
     * Show the form for creating a new product
     */
    public function create(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Ready to create a new product',
            'required_fields' => [
                'name' => 'string (required, max: 255)',
                'description' => 'text (optional)',
                'price' => 'decimal (required, format: 99999999.99)',
                'stock' => 'integer (optional, default: 0)',
                'image' => 'file (optional, image format)'
            ]
        ]);
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0|max:999999.99',
                'stock' => 'nullable|integer|min:0',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $product = $this->productService->createProduct($validatedData);

            return response()->json([
                'status' => 'success',
                'message' => 'Product created successfully',
                'data' => $product
            ], JsonResponse::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create product',
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified product
     */
    public function show($id): JsonResponse
    {
        try {
            $product = $this->productService->getProductById($id);
            
            return response()->json([
                'status' => 'success',
                'data' => $product
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }

    /**
     * Show the form for editing the specified product
     */
    public function edit($id): JsonResponse
    {
        try {
            $product = $this->productService->getProductById($id);
            
            return response()->json([
                'status' => 'success',
                'data' => $product,
                'editable_fields' => [
                    'name' => 'string (max: 255)',
                    'description' => 'text (optional)',
                    'price' => 'decimal (format: 99999999.99)',
                    'stock' => 'integer (min: 0)',
                    'image' => 'file (optional, image format)'
                ]
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'sometimes|required|numeric|min:0|max:999999.99',
                'stock' => 'nullable|integer|min:0',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $product = $this->productService->updateProduct($id, $validatedData);

            return response()->json([
                'status' => 'success',
                'message' => 'Product updated successfully',
                'data' => $product
            ], JsonResponse::HTTP_OK);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update product',
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified product
     */
    public function destroy($id): JsonResponse
    {
        try {
            $this->productService->deleteProduct($id);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Product deleted successfully'
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete product',
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get products with low stock
     */
    public function lowStock(Request $request): JsonResponse
    {
        try {
            $threshold = $request->get('threshold', 10);
            $products = $this->productService->getLowStockProducts($threshold);
            
            return response()->json([
                'status' => 'success',
                'data' => $products,
                'threshold' => $threshold
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve low stock products',
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update product stock
     */
    public function updateStock(Request $request, $id): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'quantity' => 'required|integer|min:0',
                'operation' => 'nullable|string|in:set,add,subtract'
            ]);

            $operation = $validatedData['operation'] ?? 'set';
            $product = $this->productService->updateStock($id, $validatedData['quantity'], $operation);

            return response()->json([
                'status' => 'success',
                'message' => 'Stock updated successfully',
                'data' => $product
            ], JsonResponse::HTTP_OK);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update stock',
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get products by price range
     */
    public function byPriceRange(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'min_price' => 'nullable|numeric|min:0',
                'max_price' => 'nullable|numeric|min:0'
            ]);

            $products = $this->productService->getProductsByPriceRange(
                $validatedData['min_price'] ?? null,
                $validatedData['max_price'] ?? null
            );

            return response()->json([
                'status' => 'success',
                'data' => $products,
                'filters' => $validatedData
            ], JsonResponse::HTTP_OK);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve products',
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get product statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = $this->productService->getProductStats();
            
            return response()->json([
                'status' => 'success',
                'data' => $stats
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve statistics',
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Manually send low stock notification for a product
     */
    public function sendLowStockNotification($id): JsonResponse
    {
        try {
            $this->productService->sendLowStockNotification($id);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Low stock notification sent successfully'
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send notification',
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

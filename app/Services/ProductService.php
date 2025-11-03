<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Jobs\Products\SendLowStockNotification;

class ProductService
{
    /**
     * Get all products with optional pagination and search
     */
    public function getAllProducts($perPage = 15, $page = 1, $search = null)
    {
        $query = Product::query();


        if ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage, ['*'], "product_page_$page", $page);
    }

    /**
     * Get a single product by ID
     */
    public function getProductById($id)
    {
        return Product::findOrFail($id);
    }

    /**
     * Create a new product
     */
    public function createProduct(array $data)
    {
        // Handle image upload if provided
        if (isset($data['image']) && $data['image']) {
            $data['image'] = $this->handleImageUpload($data['image']);
        }

        return Product::create($data);
    }

    /**
     * Update an existing product
     */
    public function updateProduct($id, array $data)
    {
        $product = $this->getProductById($id);
        
        // Handle image upload if provided
        if (isset($data['image']) && $data['image']) {
            // Delete old image if it exists
            if ($product->image && Storage::exists($product->image)) {
                Storage::delete($product->image);
            }
            $data['image'] = $this->handleImageUpload($data['image']);
        }

        $product->update($data);
        return $product->fresh();
    }

    /**
     * Delete a product
     */
    public function deleteProduct($id)
    {
        $product = $this->getProductById($id);
        
        // Delete associated image if it exists
        if ($product->image && Storage::exists($product->image)) {
            Storage::delete($product->image);
        }

        return $product->delete();
    }

    /**
     * Get products with low stock (configurable threshold)
     */
    public function getLowStockProducts($threshold = 10)
    {
        return Product::where('stock', '<=', $threshold)
                     ->orderBy('stock', 'asc')
                     ->get();
    }

    /**
     * Update product stock
     */
    public function updateStock($id, $quantity, $operation = 'set')
    {
        $product = $this->getProductById($id);
        
        switch ($operation) {
            case 'add':
                $product->stock += $quantity;
                break;
            case 'subtract':
                $product->stock = max(0, $product->stock - $quantity);
                break;
            case 'set':
            default:
                $product->stock = max(0, $quantity);
                break;
        }

        $product->save();
        
        // Check if we need to send low stock notification
        $this->checkAndSendLowStockNotification($product);
        
        return $product;
    }

    /**
     * Check if product needs low stock notification and dispatch job
     */
    private function checkAndSendLowStockNotification(Product $product)
    {
        $threshold = config('product.notifications.low_stock_threshold', 20);
        $autoAlertsEnabled = config('product.notifications.auto_alerts.enabled', false);

        if ($autoAlertsEnabled && $product->stock <= $threshold) {
            $queue = config('product.notifications.auto_alerts.queue', 'default');
            $delay = config('product.notifications.auto_alerts.delay_minutes', 0);

            if ($delay > 0) {
                SendLowStockNotification::dispatch($product)
                    ->onQueue($queue)
                    ->delay(now()->addMinutes($delay));
            } else {
                SendLowStockNotification::dispatch($product)
                    ->onQueue($queue);
            }
        }
    }

    /**
     * Manually send low stock notification for a product
     */
    public function sendLowStockNotification($id)
    {
        $product = $this->getProductById($id);
        SendLowStockNotification::dispatch($product);
        return true;
    }

    /**
     * Get products by price range
     */
    public function getProductsByPriceRange($minPrice = null, $maxPrice = null)
    {
        $query = Product::query();

        if ($minPrice !== null) {
            $query->where('price', '>=', $minPrice);
        }

        if ($maxPrice !== null) {
            $query->where('price', '<=', $maxPrice);
        }

        return $query->orderBy('price', 'asc')->get();
    }

    /**
     * Handle image upload.
     */
    private function handleImageUpload($image)
    {
        if ($image) {
            return $image->store('products', 'public');
        }
        return null;
    }

    /**
     * Get product statistics.
     */
    public function getProductStats()
    {
        return [
            'total_products' => Product::count(),
            'total_value' => Product::sum(DB::raw('price * stock')),
            'average_price' => Product::avg('price'),
            'out_of_stock' => Product::where('stock', 0)->count(),
            'low_stock' => Product::where('stock', '>', 0)->where('stock', '<=', 10)->count(),
        ];
    }
}
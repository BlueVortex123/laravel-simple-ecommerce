<?php

namespace App\Observers;

use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Support\Facades\Log;
use App\Jobs\Products\SendLowStockNotification;

class ProductObserver
{
    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        $productChanges = $product->getChanges();
        if (empty($productChanges)) return;

        if (array_key_exists('Stock', $productChanges)) {
            logger('Stock updated for product : ' . $product->Name . '. New stock: ' . $productChanges['Stock']);

            $lowStockThreshold = config('product.notifications.stock_threshold_for_alert', 5);
            $notifyAdmin = config('product.notifications.notify_admin_on_low_stock', true);

            if ($notifyAdmin && $product->Stock <= $lowStockThreshold) {
                Log::alert('Stock for product ' . $product->Name . ' is low. Current stock: ' . $product->Stock);
                app(ProductService::class)->sendLowStockNotification($product->id);
            }
        }

    }
}

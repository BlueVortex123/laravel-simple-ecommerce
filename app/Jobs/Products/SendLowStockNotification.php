<?php

namespace App\Jobs\Products;

use App\Models\Product;
use App\Mail\LowStockAlert;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Foundation\Bus\Dispatchable;

class SendLowStockNotification implements ShouldQueue
{
    use Queueable;

    public $tries = 5;
    public $backoff = 180;

    /**
     * Create a new job instance.
     */
    public function __construct(public Product $product)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to(config('product.notifications.admin_email'))->send(new LowStockAlert($this->product));
    }
}

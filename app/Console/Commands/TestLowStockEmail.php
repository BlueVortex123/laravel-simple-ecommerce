<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Mail\LowStockAlert;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestLowStockEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test-low-stock {product_id?} {--email=admin@example.com}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the low stock email notification with a specific product';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $productId = $this->argument('product_id');
        $email = $this->option('email');

        try {
            // If no product ID provided, get the first product or create a test one
            if (!$productId) {
                $product = Product::first();
                
                if (!$product) {
                    $this->error('No products found. Please run the seeder first: php artisan db:seed --class=ProductSeeder');
                    return 1;
                }
            } else {
                $product = Product::findOrFail($productId);
            }

            $this->info("Sending low stock alert for product: {$product->name}");
            $this->info("Current stock level: {$product->stock}");
            $this->info("Sending email to: {$email}");

            // Send the email
            Mail::to($email)->send(new LowStockAlert($product));

            $this->info("Low stock alert email sent successfully!");
            
            // Display product details
            $this->table(
                ['Property', 'Value'],
                [
                    ['ID', $product->id],
                    ['Name', $product->name],
                    ['Price', '$' . number_format($product->price, 2)],
                    ['Stock', $product->stock],
                    ['Description', \Illuminate\Support\Str::limit($product->description ?? 'No description', 50)],
                ]
            );

        } catch (\Exception $e) {
            $this->error("Failed to send email: {$e->getMessage()}");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}

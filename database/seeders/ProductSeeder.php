<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Laptop Dell XPS 13',
                'description' => 'Ultra-portable laptop with Intel Core i7 processor, 16GB RAM, and 512GB SSD. Perfect for professionals and students.',
                'price' => 1299.99,
                'stock' => 25,
                'image' => 'products/SampleProductImage.png',
            ],
            [
                'name' => 'iPhone 15 Pro',
                'description' => 'Latest iPhone with A17 Pro chip, 256GB storage, and advanced camera system. Available in multiple colors.',
                'price' => 999.99,
                'stock' => 50,
                'image' => 'products/SampleProductImage.png',
            ],
            [
                'name' => 'Samsung 4K Smart TV 55"',
                'description' => '55-inch 4K UHD Smart TV with HDR support, built-in streaming apps, and voice control.',
                'price' => 699.99,
                'stock' => 15,
                'image' => 'products/SampleProductImage.png',
            ],
            [
                'name' => 'Sony WH-1000XM5 Headphones',
                'description' => 'Premium noise-canceling wireless headphones with 30-hour battery life and superior sound quality.',
                'price' => 349.99,
                'stock' => 40,
                'image' => 'products/SampleProductImage.png',
            ],
            [
                'name' => 'Gaming Chair Pro',
                'description' => 'Ergonomic gaming chair with lumbar support, adjustable armrests, and premium PU leather upholstery.',
                'price' => 299.99,
                'stock' => 20,
                'image' => 'products/SampleProductImage.png',
            ],
            [
                'name' => 'Mechanical Gaming Keyboard',
                'description' => 'RGB backlit mechanical keyboard with Cherry MX switches, programmable keys, and anti-ghosting technology.',
                'price' => 129.99,
                'stock' => 35,
                'image' => 'products/SampleProductImage.png',
            ],
            [
                'name' => 'Wireless Mouse Pro',
                'description' => 'High-precision wireless mouse with adjustable DPI, ergonomic design, and long-lasting battery.',
                'price' => 79.99,
                'stock' => 60,
                'image' => 'products/SampleProductImage.png',
            ],
            [
                'name' => 'External Hard Drive 2TB',
                'description' => 'Portable 2TB external hard drive with USB 3.0 connectivity and automatic backup software.',
                'price' => 89.99,
                'stock' => 45,
                'image' => 'products/SampleProductImage.png',
            ],
            [
                'name' => 'Bluetooth Speaker Waterproof',
                'description' => 'Portable Bluetooth speaker with 360-degree sound, waterproof design, and 12-hour battery life.',
                'price' => 59.99,
                'stock' => 55,
                'image' => 'products/SampleProductImage.png',
            ],
            [
                'name' => 'Smartphone Stand Adjustable',
                'description' => 'Universal adjustable phone stand compatible with all smartphone sizes, perfect for video calls and media viewing.',
                'price' => 24.99,
                'stock' => 100,
                'image' => 'products/SampleProductImage.png',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}

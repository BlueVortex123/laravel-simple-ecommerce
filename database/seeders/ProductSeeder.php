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
            [
                'name' => 'Tablet Pro 11',
                'description' => '11-inch tablet with Retina display, 128GB storage, and long battery life.',
                'price' => 499.99,
                'stock' => 80,
                'image' => 'products/SampleProductImage.png',
            ],
            [
                'name' => 'Smartwatch Series 8',
                'description' => 'Feature-rich smartwatch with ECG, GPS, and water resistance up to 50m.',
                'price' => 399.99,
                'stock' => 70,
                'image' => 'products/SampleProductImage.png',
            ],
            [
                'name' => 'Canon EOS R50 Mirrorless Camera',
                'description' => 'Compact mirrorless camera with 24MP sensor and 4K video recording.',
                'price' => 849.99,
                'stock' => 18,
                'image' => 'products/SampleProductImage.png',
            ],
            [
                'name' => 'GoPro HERO11 Action Camera',
                'description' => 'Rugged action camera with 5.3K video, HyperSmooth stabilization, and waterproof body.',
                'price' => 399.99,
                'stock' => 30,
                'image' => 'products/SampleProductImage.png',
            ],
            [
                'name' => 'True Wireless Earbuds',
                'description' => 'Compact earbuds with active noise canceling and wireless charging case.',
                'price' => 149.99,
                'stock' => 120,
                'image' => 'products/SampleProductImage.png',
            ],
            [
                'name' => 'Portable Monitor 15.6"',
                'description' => 'Lightweight USB-C portable monitor with full HD resolution, ideal for travel.',
                'price' => 199.99,
                'stock' => 40,
                'image' => 'products/SampleProductImage.png',
            ],
            [
                'name' => 'VR Headset Quest 2',
                'description' => 'Standalone VR headset with immersive display and intuitive controllers.',
                'price' => 299.99,
                'stock' => 25,
                'image' => 'products/SampleProductImage.png',
            ],
            [
                'name' => 'Mesh WiFi Router',
                'description' => 'Tri-band mesh WiFi system for whole-home coverage and fast streaming speeds.',
                'price' => 229.99,
                'stock' => 60,
                'image' => 'products/SampleProductImage.png',
            ],
            [
                'name' => 'Network Attached Storage 4TB',
                'description' => '2-bay NAS with 4TB capacity, RAID support, and remote access features.',
                'price' => 299.99,
                'stock' => 22,
                'image' => 'products/SampleProductImage.png',
            ],
            [
                'name' => 'Graphics Drawing Tablet',
                'description' => 'Pen tablet with high-precision stylus, customizable shortcuts, and large active area.',
                'price' => 89.99,
                'stock' => 75,
                'image' => 'products/SampleProductImage.png',
            ],
            [
                'name' => 'Portable SSD 1TB',
                'description' => 'High-speed NVMe portable SSD with USB-C connectivity and durable casing.',
                'price' => 159.99,
                'stock' => 90,
                'image' => 'products/SampleProductImage.png',
            ],
            [
                'name' => '27" QHD Monitor',
                'description' => '27-inch QHD monitor with IPS panel, 75Hz refresh rate, and slim bezels.',
                'price' => 279.99,
                'stock' => 33,
                'image' => 'products/SampleProductImage.png',
            ],
            [
                'name' => 'Electric Standing Desk',
                'description' => 'Height-adjustable electric desk with memory presets and sturdy steel frame.',
                'price' => 449.99,
                'stock' => 12,
                'image' => 'products/SampleProductImage.png',
            ],
            [
                'name' => 'LED Desk Lamp',
                'description' => 'Dimmable LED desk lamp with adjustable color temperature and USB charging port.',
                'price' => 39.99,
                'stock' => 150,
                'image' => 'products/SampleProductImage.png',
            ],
            [
                'name' => 'Fitness Tracker Band',
                'description' => 'Lightweight fitness band with heart rate monitoring, sleep tracking, and GPS sync.',
                'price' => 69.99,
                'stock' => 140,
                'image' => 'products/SampleProductImage.png',
            ],
            [
                'name' => 'Smart Thermostat',
                'description' => 'WiFi-enabled smart thermostat with learning capabilities and energy reports.',
                'price' => 249.99,
                'stock' => 28,
                'image' => 'products/SampleProductImage.png',
            ],
            [
                'name' => 'Espresso Coffee Maker',
                'description' => 'Compact espresso machine with milk frother and programmable brew settings.',
                'price' => 189.99,
                'stock' => 36,
                'image' => 'products/SampleProductImage.png',
            ],
            [
                'name' => 'Air Purifier HEPA',
                'description' => 'True HEPA air purifier with multi-stage filtration and quiet night mode.',
                'price' => 129.99,
                'stock' => 48,
                'image' => 'products/SampleProductImage.png',
            ],
            [
                'name' => 'Robot Vacuum Cleaner',
                'description' => 'Smart robot vacuum with mapping, scheduling, and strong suction power.',
                'price' => 279.99,
                'stock' => 26,
                'image' => 'products/SampleProductImage.png',
            ],
            [
                'name' => 'Electric Scooter 250W',
                'description' => 'Foldable electric scooter with 25km range, 250W motor, and front/rear lights.',
                'price' => 519.99,
                'stock' => 14,
                'image' => 'products/SampleProductImage.png',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}

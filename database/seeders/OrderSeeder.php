<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Faker\Factory as Faker;
use App\Enums\OrderStatusEnum;
use Illuminate\Database\Seeder;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        
        // Get all users and products
        $users = User::all();
        $products = Product::all();
        
        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->warn('Please seed users and products first!');
            return;
        }

        $statuses = OrderStatusEnum::cases();
        $paymentMethods = PaymentMethodEnum::cases();
        $paymentStatuses = PaymentStatusEnum::cases();

        // Create 30 sample orders
        for ($i = 1; $i <= 30; $i++) {
            $user = $users->random();
            $status = $faker->randomElement($statuses);
            $paymentStatus = $status === 'delivered' ? 'completed' : $faker->randomElement($paymentStatuses);
            
            // Create shipping address
            $shippingAddress = [
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'company' => $faker->optional()->company,
                'address_line_1' => $faker->streetAddress,
                'address_line_2' => $faker->optional()->secondaryAddress,
                'city' => $faker->city,
                'state' => $faker->state,
                'postal_code' => $faker->postcode,
                'country' => 'United States',
                'phone' => $faker->phoneNumber,
            ];
            
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => strtoupper($faker->bothify('ORD-#####')),
                'status' => $status,
                'shipping_address' => $shippingAddress,
                'billing_address' => $faker->boolean(70) ? $shippingAddress : null,
                'payment_method' => $faker->randomElement($paymentMethods),
                'payment_status' => $paymentStatus,
                'total_amount' => 0, // Temporary value, will be updated
                'shipped_at' => in_array($status, ['shipped', 'delivered']) ? $faker->dateTimeBetween('-30 days', 'now') : null,
                'delivered_at' => $status === 'delivered' ? $faker->dateTimeBetween('-15 days', 'now') : null,
                'notes' => $faker->optional(20)->sentence,
                'created_at' => $faker->dateTimeBetween('-60 days', 'now'),
            ]);

            // Add 1-5 random products to each order
            $orderProducts = $products->random($faker->numberBetween(1, 5));
            $subtotal = 0;

            foreach ($orderProducts as $product) {
                $quantity = $faker->numberBetween(1, 3);
                $unitPrice = $product->price;
                $totalPrice = $quantity * $unitPrice;
                $subtotal += $totalPrice;

                // Create product snapshot at time of order
                $productSnapshot = [
                    'name' => $product->name,
                    'description' => $product->description,
                    'image' => $product->image,
                    'original_price' => $product->price,
                ];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'product_snapshot' => $productSnapshot,
                ]);
            }


            // Update order with calculated amounts
            $order->update([
                'total_amount' => $subtotal,
            ]);
        }

        $this->command->info('Created 20 sample orders with order items!');
        
        // Display summary
        $orderCount = Order::count();
        $orderItemCount = OrderItem::count();
        $totalRevenue = Order::where('payment_status', 'completed')->sum('total_amount');
        
        $this->command->table(
            ['Metric', 'Value'],
            [
                ['Total Orders', $orderCount],
                ['Total Order Items', $orderItemCount],
                ['Total Revenue (Completed)', '$' . number_format($totalRevenue, 2)],
                ['Pending Orders', Order::where('status', 'pending')->count()],
                ['Delivered Orders', Order::where('status', 'delivered')->count()],
            ]
        );
    }
}

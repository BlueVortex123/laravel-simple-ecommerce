<?php

namespace App\Services;

use App\Enums\PaymentMethodEnum;
use App\Models\Product;
use Faker\Provider\ar_EG\Payment;
use Illuminate\Support\Facades\Auth;

class TestDataService
{
    /**
     * Get sample order data for testing
     */
    public static function getSampleOrderData(): array
    {
        return [
            'user_id' => Auth::id(),
            'items' => self::getSampleItems(),
            'order_data' => self::getSampleOrderDetails(),
        ];
    }

    /**
     * Get sample items with available products
     */
    public static function getSampleItems(): array
    {
        $availableProducts = Product::where('stock', '>', 0)->take(3)->get();
        
        if ($availableProducts->isEmpty()) {
            // Fallback to any products if no stock available
            $availableProducts = Product::take(2)->get();
        }

        $items = [];
        foreach ($availableProducts->take(2) as $index => $product) {
            $items[] = [
                'product_id' => $product->id,
                'quantity' => $index === 0 ? 1 : 2, // Vary quantities
            ];
        }

        return $items;
    }

    /**
     * Get sample order details (shipping, billing, etc.)
     */
    public static function getSampleOrderDetails(): array
    {
        return [
            'shipping_address' => self::getSampleShippingAddress(),
            'billing_address' => self::getSampleBillingAddress(),
            'payment_method' => PaymentMethodEnum::CREDIT_CARD,
            'notes' => 'Test order - Please handle with care.',
        ];
    }

    /**
     * Get sample shipping address
     */
    public static function getSampleShippingAddress(): array
    {
        return [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'company' => 'Tech Solutions Inc',
            'address_line_1' => '123 Innovation Drive',
            'address_line_2' => 'Suite 200',
            'city' => 'San Francisco',
            'state' => 'CA',
            'postal_code' => '94105',
            'country' => 'United States',
            'phone' => '+1-555-123-4567',
        ];
    }

    /**
     * Get sample billing address
     */
    public static function getSampleBillingAddress(): array
    {
        return [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'company' => 'Business Consulting LLC',
            'address_line_1' => '456 Commerce Street',
            'address_line_2' => 'Floor 15',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'country' => 'United States',
        ];
    }

    /**
     * Get random sample data for variety in testing
     */
    public static function getRandomOrderData(): array
    {
        $addresses = [
            [
                'first_name' => 'Alice',
                'last_name' => 'Johnson',
                'city' => 'Los Angeles',
                'state' => 'CA',
                'postal_code' => '90210',
            ],
            [
                'first_name' => 'Bob',
                'last_name' => 'Wilson',
                'city' => 'Chicago',
                'state' => 'IL',
                'postal_code' => '60601',
            ],
            [
                'first_name' => 'Carol',
                'last_name' => 'Brown',
                'city' => 'Miami',
                'state' => 'FL',
                'postal_code' => '33101',
            ],
        ];

        $randomAddress = $addresses[array_rand($addresses)];
        
        return [
            'user_id' => Auth::id(),
            'items' => self::getSampleItems(),
            'order_data' => [
                'shipping_address' => array_merge($randomAddress, [
                    'company' => 'Random Corp',
                    'address_line_1' => rand(100, 999) . ' Main Street',
                    'country' => 'United States',
                    'phone' => '+1-555-' . rand(100, 999) . '-' . rand(1000, 9999),
                ]),
                'payment_method' => ['credit_card', 'paypal', 'bank_transfer'][array_rand(['credit_card', 'paypal', 'bank_transfer'])],
                'notes' => 'Random test order #' . rand(1000, 9999),
            ],
        ];
    }

    /**
     * Get order data from environment variables (for different environments)
     */
    public static function getConfigurableOrderData(): array
    {
        return [
            'user_id' => Auth::id(),
            'items' => [
                [
                    'product_id' => rand(1, count(Product::all())),
                    'quantity' => rand(1, 3),
                ],
                [
                    'product_id' => rand(1, count(Product::all())),
                    'quantity' => rand(1, 3),
                ],
            ],
            'order_data' => [
                'shipping_address' => [
                    'first_name' => env('TEST_SHIPPING_FIRST_NAME', 'Test'),
                    'last_name' => env('TEST_SHIPPING_LAST_NAME', 'User'),
                    'address_line_1' => env('TEST_SHIPPING_ADDRESS', '123 Test St'),
                    'city' => env('TEST_SHIPPING_CITY', 'Test City'),
                    'state' => env('TEST_SHIPPING_STATE', 'TS'),
                    'postal_code' => env('TEST_SHIPPING_ZIP', '12345'),
                    'country' => env('TEST_SHIPPING_COUNTRY', 'United States'),
                    'phone' => env('TEST_SHIPPING_PHONE', '555-0123'),
                ],
                'payment_method' => env('TEST_PAYMENT_METHOD', 'credit_card'),
                'notes' => env('TEST_ORDER_NOTES', 'Test order from environment config'),
            ],
        ];
    }
}
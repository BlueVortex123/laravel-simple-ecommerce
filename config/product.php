<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Product Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration settings for product management, notifications, and alerts.
    |
    */

    'notifications' => [
        /*
        |--------------------------------------------------------------------------
        | Admin Email for Notifications
        |--------------------------------------------------------------------------
        |
        | Email address where admin notifications will be sent.
        |
        */
        'admin_email' => env('PRODUCT_ADMIN_EMAIL', 'admin@example.com'),

        /*
        |--------------------------------------------------------------------------
        | Low Stock Threshold
        |--------------------------------------------------------------------------
        |
        | Default threshold for low stock alerts. When product stock falls
        | below or equals this number, a notification will be triggered.
        |
        */
        'low_stock_threshold' => env('PRODUCT_LOW_STOCK_THRESHOLD', 20),

        /*
        |--------------------------------------------------------------------------
        | Auto Alert Settings
        |--------------------------------------------------------------------------
        |
        | Configure automatic alert behavior for stock management.
        |
        */
        'auto_alerts' => [
            'enabled' => env('PRODUCT_AUTO_ALERTS_ENABLED', true),
            'queue' => env('PRODUCT_ALERTS_QUEUE', 'default'),
            'delay_minutes' => env('PRODUCT_ALERTS_DELAY', 0),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for product image handling.
    |
    */
    'images' => [
        'default_image' => 'SampleProductImage.png',
        'storage_path' => 'products',
        'max_size_mb' => 2,
        'allowed_types' => ['jpeg', 'png', 'jpg', 'webp'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination Settings
    |--------------------------------------------------------------------------
    |
    | Default pagination settings for product listings.
    |
    */
    'pagination' => [
        'per_page' => env('PRODUCT_PER_PAGE', 15),
        'max_per_page' => env('PRODUCT_MAX_PER_PAGE', 100),
    ],
];
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Test Data Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration settings for generating test data.
    |
    */

    'test_data' => [
        /*
        |--------------------------------------------------------------------------
        | Test Data Generation Settings
        |--------------------------------------------------------------------------
        |
        | Controls when and how test data is generated automatically.
        |
        */
        'generate_orders' => env('TEST_DATA_GENERATE_ORDERS', false), // Disabled by default
        
        /*
        |--------------------------------------------------------------------------
        | Scheduling Configuration
        |--------------------------------------------------------------------------
        |
        | Configure how often the scheduled job runs in seconds.
        |
        */
        'schedule_frequency' => env('TEST_DATA_SCHEDULE_FREQUENCY', 10),

        /*
        |--------------------------------------------------------------------------
        | Test Data Generation Limits
        |--------------------------------------------------------------------------
        |
        | Limits for generating test data to avoid excessive resource usage.
        |
        */
        'max_orders_per_run' => env('TEST_DATA_MAX_ORDERS_PER_RUN', 3),
        'order_types' => env('TEST_DATA_ORDER_TYPES', 'random'),
    ],
];
<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule the TestOrderCommand with parameters from config
$schedule = app(Schedule::class);

$scheduleCommand = $schedule->command('test:order', [
    '--type' => config('testdata.test_data.order_types', 'random'),
    '--count' => config('testdata.test_data.max_orders_per_run', 3),
])
    ->when(function () {
        // Only run when test data generation is enabled
        return config('testdata.test_data.generate_orders', false);
    })
    ->everyMinute() // TBD to replace with dynamic frequency based on config
    ->withoutOverlapping() // Prevent multiple instances from running simultaneously
    ->runInBackground() // Run in background to avoid blocking
    ->appendOutputTo(storage_path('logs/test-orders.log')); // Log output

;

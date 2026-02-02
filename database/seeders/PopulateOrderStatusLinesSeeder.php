<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\OrderStatusLine;

class PopulateOrderStatusLinesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = Order::all();

        foreach ($orders as $order) {
            // Skip if already has status lines
            if (OrderStatusLine::where('order_id', $order->id)->exists()) {
                continue;
            }

            // Try to determine the status name from the order model cast
            $rawStatus = $order->getAttribute('status');

            // Handle PHP 8.1 backed enums and other types
            if ($rawStatus instanceof \BackedEnum) {
                $statusName = $rawStatus->value;
            } else {
                $statusName = is_scalar($rawStatus) ? (string) $rawStatus : null;
            }

            if (empty($statusName)) {
                continue;
            }

            // Try exact match then case-insensitive
            $status = OrderStatus::where('name', $statusName)->first();
            if (!$status) {
                $status = OrderStatus::whereRaw('LOWER(name) = ?', [strtolower($statusName)])->first();
            }

            if (!$status) {
                // skip if we cannot map order.status to an existing order_status
                continue;
            }

            // Create a status line
            OrderStatusLine::create([
                'order_id' => $order->id,
                'order_status_id' => $status->id,
            ]);

            // Update denormalized column
            $order->last_order_status_id = $status->id;
            $order->saveQuietly();
        }
    }
}

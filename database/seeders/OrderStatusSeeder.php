<?php

namespace Database\Seeders;

use App\Models\OrderStatus;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all order statuses from the enum
        $statuses = \App\Enums\OrderStatusEnum::cases();
        foreach ($statuses as $status) {
            OrderStatus::firstOrCreate(
                ['name' => $status],
            );
        }
    }
}

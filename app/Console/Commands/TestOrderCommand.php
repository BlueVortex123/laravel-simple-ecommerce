<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\OrderService;
use App\Services\TestDataService;
use Illuminate\Console\Command;

class TestOrderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:order 
                            {--user-id= : User ID to place order for}
                            {--type=sample : Type of test data (sample, random, config)}
                            {--count=1 : Number of orders to create}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create test orders using TestDataService';

    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        parent::__construct();
        $this->orderService = $orderService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user-id');
        $type = $this->option('type');
        $count = (int) $this->option('count');

        // Get user
        if ($userId) {
            $user = User::find($userId);
            if (!$user) {
                $this->error("User with ID {$userId} not found.");
                return 1;
            }
        } else {
            $user = User::first();
            if (!$user) {
                $this->error("No users found. Please run user seeder first.");
                return 1;
            }
        }

        $this->info("Creating {$count} test order(s) for user: {$user->name} (ID: {$user->id})");
        $this->info("Test data type: {$type}");
        $this->newLine();

        $successCount = 0;
        $failures = [];

        for ($i = 1; $i <= $count; $i++) {
            try {
                // Get test data based on type
                $testData = $this->getTestData($type);
                $testData['user_id'] = $user->id;

                $this->line("Creating order {$i}...");

                $order = $this->orderService->storeOrder(
                    $testData['user_id'],
                    $testData['items'],
                    $testData['order_data']
                );

                $this->info("Order created: {$order->order_number} (ID: {$order->id}) - Total: \${$order->total_amount}");
                $successCount++;

            } catch (\Exception $e) {
                $this->error("Failed to create order {$i}: " . $e->getMessage());
                $failures[] = "Order {$i}: " . $e->getMessage();
            }
        }

        $this->newLine();
        $this->info("Summary:");
        $this->info("  ✅ Successfully created: {$successCount} orders");
        $this->info("  ❌ Failed: " . (count($failures)) . " orders");

        if (!empty($failures)) {
            $this->newLine();
            $this->error("Failures:");
            foreach ($failures as $failure) {
                $this->error("  - {$failure}");
            }
        }

        return 0;
    }

    /**
     * Get test data based on type
     */
    private function getTestData(string $type): array
    {
        return match ($type) {
            'random' => TestDataService::getRandomOrderData(),
            'config' => TestDataService::getConfigurableOrderData(),
            'sample' => TestDataService::getSampleOrderData(),
            default => TestDataService::getSampleOrderData(),
        };
    }
}

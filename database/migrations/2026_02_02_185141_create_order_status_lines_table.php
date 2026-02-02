<?php

use App\Models\OrderStatus;
use Illuminate\Support\Facades\Schema;
use Database\Seeders\OrderStatusSeeder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_status_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_status_id')->constrained('order_statuses')->onDelete('cascade');
            $table->timestamps();

            // Indexes to optimize lookups and latest-status queries
            $table->index('order_id');
            $table->index('order_status_id');
            $table->index(['order_id', 'created_at']);
        });

        // ensure we have order_statuses table
        if (!Schema::hasTable('order_statuses')) {
            throw new \Exception('The order_statuses table does not exist. Please run the migration to create it first.');
        }

        // Check if order_statuses table is populated
        if (OrderStatus::count() === 0) {
            // Populate order_statuses table from OrderStatusEnum
            $seeder = new OrderStatusSeeder();
            $seeder->run();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_status_lines');
    }
};

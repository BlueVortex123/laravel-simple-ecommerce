<?php

namespace App\Resources;

use App\Models\Order;
use Spatie\LaravelData\Data;

class OrderViewResource extends Data
{
    public function __construct(
        public Order $order,
    ) {}

    /**
     * Create a resource from a VtexCategory model.
     */
    public static function fromModel(Order $order): self
    {
        return new self(
            order: $order,
        );
    }

    /**
     * Export resource fields as array.
     */
    public function toArray(): array
    {
        $orderFields = $this->order->toArray();
        $orderStatusesArrayValues = $this->order->statusLines->map(function ($statusLine) {
            $statusLine['value'] = $statusLine->status->name;
            return $statusLine;
        })->toArray();

        $mappedFields = array_merge($orderFields, [
            'order_statuses_values' => $orderStatusesArrayValues
        ]);

        return $mappedFields;
    }
}

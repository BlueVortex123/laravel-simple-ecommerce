<?php

namespace App\Enums;

enum OrderStatusEnum: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';

    public static function labels(): array
    {
        return [
            self::PENDING->value => 'Pending',
            self::PROCESSING->value => 'Processing',
            self::SHIPPED->value => 'Shipped',
            self::DELIVERED->value => 'Delivered',
            self::CANCELLED->value => 'Cancelled',
        ];
    }
}

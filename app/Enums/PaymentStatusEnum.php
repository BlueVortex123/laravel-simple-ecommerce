<?php

namespace App\Enums;

enum PaymentStatusEnum: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';

    public static function labels(): array
    {
        return [
            self::PENDING->value => 'Pending',
            self::COMPLETED->value => 'Completed',
            self::FAILED->value => 'Failed',
            self::REFUNDED->value => 'Refunded',
        ];
    }
}

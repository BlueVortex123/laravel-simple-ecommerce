<?php

namespace App\Enums;

enum PaymentMethodEnum: string
{
    case CREDIT_CARD = 'credit_card';
    case PAYPAL = 'paypal';
    case BANK_TRANSFER = 'bank_transfer';
    case CASH_ON_DELIVERY = 'cash_on_delivery';

    public static function labels(): array
    {
        return [
            self::CREDIT_CARD->value => 'Credit Card',
            self::PAYPAL->value => 'PayPal',
            self::BANK_TRANSFER->value => 'Bank Transfer',
            self::CASH_ON_DELIVERY->value => 'Cash on Delivery',
        ];
    }
}

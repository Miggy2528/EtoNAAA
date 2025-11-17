<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case FOR_DELIVERY = 'for_delivery';
    case COMPLETE = 'complete';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => __('Pending'),
            self::FOR_DELIVERY => __('For Delivery'),
            self::COMPLETE => __('Complete'),
            self::CANCELLED => __('Cancelled'),
        };
    }
}

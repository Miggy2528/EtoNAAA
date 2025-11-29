<?php

namespace App\Enums;

enum PurchaseStatus: int
{
    case PENDING = 0;
    case APPROVED = 1;
    case FOR_DELIVERY = 2;
    case COMPLETE = 3;
    case RECEIVED = 4;

    public function label(): string
    {
        return match ($this) {
            self::PENDING => __('Pending'),
            self::APPROVED => __('Approved'),
            self::FOR_DELIVERY => __('For Delivery'),
            self::COMPLETE => __('Complete'),
            self::RECEIVED => __('Received'),
        };
    }
}

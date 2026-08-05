<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Awaiting = 'awaiting';
    case Processing = 'processing';
    case Confirmed = 'confirmed';
    case WaitingForConfirmation = 'waiting_for_confirmation';
    case Shipped = 'shipped';
    case InTransit = 'in_transit';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Awaiting => 'Awaiting',self::Processing => 'Processing',self::Confirmed => 'Confirmed',self::WaitingForConfirmation => 'Waiting for Confirmation',self::Shipped => 'Shipped',self::InTransit => 'In Transit',self::Delivered => 'Delivered',self::Cancelled => 'Cancelled'
        };
    }
}

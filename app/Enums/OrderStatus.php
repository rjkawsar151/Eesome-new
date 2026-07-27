<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Awaiting = 'awaiting';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case InTransit = 'in_transit';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Awaiting => 'Awaiting',self::Processing => 'Processing',self::Shipped => 'Shipped',self::InTransit => 'In Transit',self::Delivered => 'Delivered',self::Cancelled => 'Cancelled'
        };
    }

    public function next(): array
    {
        return match ($this) {
            self::Awaiting => [self::Processing, self::Cancelled],self::Processing => [self::Shipped, self::Cancelled],self::Shipped => [self::InTransit, self::Delivered],self::InTransit => [self::Delivered],self::Delivered,self::Cancelled => []
        };
    }
}

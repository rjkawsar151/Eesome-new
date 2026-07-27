<?php

namespace App\Services;

use App\Models\ShippingMethod;
use RuntimeException;

class ShippingCalculator
{
    public function method(?string $code): ShippingMethod
    {
        $query = ShippingMethod::query()->where('is_active', true);
        $method = $code
            ? $query->where('code', $code)->first()
            : $query->orderBy('sort_order')->first();

        if (! $method) {
            throw new RuntimeException('The selected delivery method is unavailable.');
        }

        return $method;
    }

    public function calculate(float $subtotal, ?string $code): float
    {
        $method = $this->method($code);

        if ($method->minimum_order_amount !== null && $subtotal < (float) $method->minimum_order_amount) {
            throw new RuntimeException("{$method->name} requires a minimum order of ৳".number_format((float) $method->minimum_order_amount, 0).'.');
        }

        if ($method->charge_type === 'free' || ($method->free_shipping_threshold !== null && $subtotal >= (float) $method->free_shipping_threshold)) {
            return 0.0;
        }

        return round((float) $method->base_charge, 2);
    }
}

<?php

namespace App\Services;

use App\Models\DeliverySetting;
use App\Models\District;

class ShippingCalculator
{
    public function calculate(float $subtotal, mixed $districtOrCode = null, ?District $district = null): float
    {
        $targetDistrict = $districtOrCode instanceof District ? $districtOrCode : $district;

        return $this->calculateForDistrict($subtotal, $targetDistrict);
    }

    public function calculateForDistrict(float $subtotal, ?District $district = null): float
    {
        $settings = DeliverySetting::getSettings();

        if ($settings->free_delivery_enabled && $subtotal >= (float) $settings->free_delivery_threshold) {
            return 0.0;
        }

        if ($district && $district->delivery_charge !== null) {
            return round((float) $district->delivery_charge, 2);
        }

        return 130.0;
    }
}


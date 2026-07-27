<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    protected $fillable = ['name', 'code', 'description', 'charge_type', 'base_charge', 'minimum_order_amount', 'free_shipping_threshold', 'estimated_delivery_days', 'cod_available', 'is_active', 'sort_order'];

    protected $casts = [
        'base_charge' => 'decimal:2',
        'minimum_order_amount' => 'decimal:2',
        'free_shipping_threshold' => 'decimal:2',
        'cod_available' => 'boolean',
        'is_active' => 'boolean',
    ];
}

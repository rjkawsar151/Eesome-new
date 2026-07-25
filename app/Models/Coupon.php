<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $table = 'coupons';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'min_order_amount',
        'expiry_date',
        'usage_limit',
        'used_count',
        'status',
    ];

    protected $casts = [
        'discount_value' => 'string',
        'min_order_amount' => 'string',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'status' => 'boolean',
        'expiry_date' => 'date',
    ];

    public function isValidForSubtotal(float $subtotal): bool
    {
        if (!$this->status) {
            return false;
        }

        if ($this->expiry_date && Carbon::parse($this->expiry_date)->endOfDay()->isPast()) {
            return false;
        }

        if (!is_null($this->usage_limit) && $this->used_count >= $this->usage_limit) {
            return false;
        }

        if ($subtotal < (float)$this->min_order_amount) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $subtotal): float
    {
        if (!$this->isValidForSubtotal($subtotal)) {
            return 0.0;
        }

        if ($this->discount_type === 'percentage') {
            $discount = ($subtotal * (float)$this->discount_value) / 100.0;
            return min($discount, $subtotal);
        }

        // Fixed discount
        return min((float)$this->discount_value, $subtotal);
    }
}

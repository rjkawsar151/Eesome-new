<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    public $timestamps = false;

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'order_number',
        'user_id',
        'customer_name',
        'email',
        'phone',
        'district',
        'thana',
        'post_office',
        'post_code',
        'shipping_address',
        'shipping_method',
        'total_amount',
        'subtotal_amount',
        'shipping_charge',
        'payment_fee',
        'discount_amount',
        'coupon_code',
        'coupon_id',
        'payment_method',
        'payment_status',
        'order_status',
        'transaction_id',
        'notes',
        'status_changed_at',
        'placed_from',
        'shipping_provider',
        'tracking_number',
        'tracking_url',
        'shipped_at',
        'estimated_delivery_at',
        'delivered_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'total_amount' => 'string',
        'subtotal_amount' => 'string',
        'shipping_charge' => 'string',
        'payment_fee' => 'string',
        'discount_amount' => 'string',
        'status_changed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'estimated_delivery_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class, 'order_id')->orderBy('created_at', 'desc');
    }

    public function paymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class, 'order_id');
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'order_items';

    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'order_id',
        'product_id',
        'variant_id',
        'product_name',
        'product_sku',
        'selected_color_name',
        'selected_color_code',
        'selected_variant',
        'product_image',
        'price',
        'quantity',
        'line_total',
        'discount_amount',
    ];

    protected $casts = [
        'price' => 'string',
        'line_total' => 'string',
        'discount_amount' => 'string',
        'quantity' => 'integer',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function getDisplayColorAttribute()
    {
        return $this->selected_color_name ?: $this->selected_variant;
    }
}

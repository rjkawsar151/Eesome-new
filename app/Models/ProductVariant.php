<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = ['product_id', 'name', 'color_name', 'color_code', 'sku', 'color', 'size', 'material', 'regular_price', 'sale_price', 'price_adjustment', 'stock', 'image', 'is_active', 'is_default', 'sort_order'];

    protected $casts = ['regular_price' => 'decimal:2', 'sale_price' => 'decimal:2', 'price_adjustment' => 'decimal:2', 'stock' => 'integer', 'is_active' => 'boolean', 'is_default' => 'boolean'];

    public function getEffectivePriceAttribute(): string
    {
        $regular = $this->regular_price ?? ((float) $this->product->price + (float) $this->price_adjustment);
        return $this->sale_price !== null && (float) $this->sale_price < (float) $regular ? (string) $this->sale_price : (string) $regular;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = ['product_id', 'name', 'color_name', 'color_code', 'sku', 'color', 'size', 'material', 'regular_price', 'sale_price', 'price_adjustment', 'stock', 'image', 'is_active', 'is_default', 'sort_order'];

    protected $casts = ['product_id' => 'integer', 'regular_price' => 'decimal:2', 'sale_price' => 'decimal:2', 'price_adjustment' => 'decimal:2', 'stock' => 'integer', 'is_active' => 'boolean', 'is_default' => 'boolean'];

    public function getEffectivePriceAttribute(): string
    {
        $basePrice = $this->regular_price !== null
            ? (float) $this->regular_price
            : ((float) ($this->product?->price ?? 0) + (float) ($this->price_adjustment ?? 0));

        if ($this->sale_price !== null && (float) $this->sale_price > 0 && (float) $this->sale_price < $basePrice) {
            return (string) $this->sale_price;
        }

        return (string) $basePrice;
    }

    public function getDisplayNameAttribute(): string
    {
        return trim($this->color_name ?: ($this->color ?: ($this->name ?: 'Default')));
    }

    public function getImagePathAttribute(): ?string
    {
        return $this->image;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

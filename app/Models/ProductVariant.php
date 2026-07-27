<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = ['product_id', 'name', 'sku', 'color', 'size', 'material', 'price_adjustment', 'stock', 'image', 'is_active', 'sort_order'];

    protected $casts = ['price_adjustment' => 'decimal:2', 'stock' => 'integer', 'is_active' => 'boolean'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

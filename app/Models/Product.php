<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    public $timestamps = false;

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'category_id',
        'brand_id',
        'sku',
        'slug',
        'name',
        'description',
        'price',
        'discount_price',
        'stock',
        'has_variants',
        'image',
        'badge_text',
        'is_featured',
        'is_new',
        'is_sold_out',
        'is_preorder',
        'is_active',
        'sort_order',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'price' => 'string',
        'discount_price' => 'string',
        'stock' => 'integer',
        'has_variants' => 'boolean',
        'is_featured' => 'boolean',
        'is_new' => 'boolean',
        'is_sold_out' => 'boolean',
        'is_preorder' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Effective price calculation
    public function getEffectivePriceAttribute(): string
    {
        if (! is_null($this->discount_price) && (float) $this->discount_price > 0 && (float) $this->discount_price < (float) $this->price) {
            return (string) $this->discount_price;
        }

        return (string) $this->price;
    }

    public function getHasDiscountAttribute(): bool
    {
        return ! is_null($this->discount_price) && (float) $this->discount_price > 0 && (float) $this->discount_price < (float) $this->price;
    }

    public function getHasStockAttribute(): bool
    {
        if ($this->has_variants) {
            $vars = $this->relationLoaded('activeVariants') ? $this->activeVariants : ($this->relationLoaded('variants') ? $this->variants : $this->activeVariants()->get());
            if ($vars->isNotEmpty()) {
                return $vars->contains(fn ($v) => (int) $v->stock > 0);
            }
        }

        return (int) $this->stock > 0;
    }

    public function getAvailableForPreorderAttribute(): bool
    {
        return ! $this->has_stock;
    }

    public function getCleanDescriptionAttribute(): string
    {
        $description = (string) $this->description;
        if ($description === '') {
            return '<p>A carefully selected handbag from the EEsome collection.</p>';
        }

        $description = preg_replace('#<(script|style)\b[^>]*>.*?</\1>#is', '', $description);
        $description = strip_tags($description, '<p><br><div><span><strong><b><em><i><ul><ol><li>');
        $description = preg_replace('/<(p|br|div|span|strong|b|em|i|ul|ol|li)\b[^>]*>/i', '<$1>', $description);

        return trim($description);
    }

    public function getBadgeInfoAttribute(): ?array
    {
        if ($this->is_sold_out) {
            return ['text' => 'SOLD OUT', 'type' => 'danger'];
        }
        if (! $this->has_stock) {
            return ['text' => 'PREORDER', 'type' => 'warning'];
        }
        if ($this->has_discount) {
            $pct = round((((float) $this->price - (float) $this->discount_price) / (float) $this->price) * 100);

            return ['text' => "-{$pct}%", 'type' => 'sale'];
        }
        if (! empty($this->badge_text)) {
            return ['text' => strtoupper($this->badge_text), 'type' => 'custom'];
        }
        if ($this->is_new) {
            return ['text' => 'NEW', 'type' => 'info'];
        }

        return null;
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order');
    }

    public function activeVariants()
    {
        return $this->hasMany(ProductVariant::class)->where('is_active', true)->orderByDesc('is_default')->orderBy('sort_order');
    }


    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id')->orderBy('sort_order');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id');
    }

    public function legacyReviews()
    {
        return $this->hasMany(ProductReview::class, 'product_id');
    }

    public function approvedReviews()
    {
        return $this->hasMany(Review::class, 'product_id')->where('status', 'approved');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'product_id');
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class, 'product_id');
    }

    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class, 'product_id');
    }

    public function scopeOrderByInStockFirst($query)
    {
        return $query->orderByRaw('
            (CASE 
                WHEN products.has_variants = 1 THEN (
                    SELECT COALESCE(SUM(v.stock), 0) 
                    FROM product_variants v 
                    WHERE v.product_id = products.id AND v.is_active = 1
                ) 
                ELSE products.stock 
            END) DESC
        ');
    }
}

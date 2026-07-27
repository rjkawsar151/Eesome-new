<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

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

    public function getAvailableForPreorderAttribute(): bool
    {
        return $this->is_preorder
            || preg_match('/\bpre[\s-]?order\b/i', strip_tags((string) $this->description)) === 1;
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

    // Badge priority helper
    public function getBadgeInfoAttribute(): ?array
    {
        if ($this->stock <= 0 && ! $this->available_for_preorder) {
            return ['text' => 'SOLD OUT', 'type' => 'danger'];
        }
        if ($this->available_for_preorder) {
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
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    protected $table = 'product_reviews';
    public const UPDATED_AT = null;

    protected $fillable = [
        'product_id',
        'user_id',
        'customer_name',
        'email',
        'rating',
        'review_text',
        'image_path',
        'status',
    ];

    protected $casts = [
        'rating' => 'integer',
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::deleted(fn (ProductReview $review) => app(\App\Services\OptimizedImageStorage::class)->delete($review->image_path));
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

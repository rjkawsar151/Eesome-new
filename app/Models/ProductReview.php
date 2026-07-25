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
        'status',
    ];

    protected $casts = [
        'rating' => 'integer',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

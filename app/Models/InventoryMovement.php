<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    protected $table = 'inventory_movements';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'product_id', 'order_id', 'type',
        'quantity_delta', 'stock_before', 'stock_after',
        'reference', 'created_by_user_id',
    ];

    protected $casts = [
        'quantity_delta' => 'integer',
        'stock_before' => 'integer',
        'stock_after' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}

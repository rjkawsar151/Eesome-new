<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    protected $table = 'order_status_histories';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'order_id', 'from_status', 'to_status',
        'changed_by_user_id', 'note',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    protected $table = 'payment_transactions';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'order_id', 'provider', 'provider_transaction_id',
        'merchant_invoice', 'amount', 'status',
        'request_payload', 'response_payload', 'verified_at',
    ];

    protected $casts = [
        'amount' => 'string',
        'request_payload' => 'array',
        'response_payload' => 'array',
        'verified_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}

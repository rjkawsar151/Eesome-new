<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = ['name', 'code', 'account_name', 'account_number', 'instructions', 'requires_transaction_id', 'is_active', 'sort_order'];

    protected $casts = ['requires_transaction_id' => 'boolean', 'is_active' => 'boolean'];
}

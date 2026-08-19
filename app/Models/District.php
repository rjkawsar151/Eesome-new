<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $table = 'districts';

    protected $fillable = [
        'division_id',
        'name',
        'delivery_charge',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'delivery_charge' => 'float',
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }
}

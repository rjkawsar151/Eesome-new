<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliverySetting extends Model
{
    use HasFactory;

    protected $table = 'delivery_settings';

    protected $fillable = [
        'free_delivery_enabled',
        'free_delivery_threshold',
    ];

    protected $casts = [
        'free_delivery_enabled' => 'boolean',
        'free_delivery_threshold' => 'float',
    ];

    public static function getSettings(): self
    {
        return self::firstOrCreate([], [
            'free_delivery_enabled' => true,
            'free_delivery_threshold' => 2000.00,
        ]);
    }
}

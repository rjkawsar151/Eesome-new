<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    protected $table = 'site_settings';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'setting_key',
        'setting_value',
    ];

    // Keys that must never be stored in DB or exposed via this model
    public const PROTECTED_KEYS = [
        'smtp_password', 'mail_password', 'db_password',
        'bkash_secret', 'api_key', 'payment_secret', 'app_key',
    ];

    public static function isProtectedKey(string $key): bool
    {
        foreach (self::PROTECTED_KEYS as $protected) {
            if (str_contains(strtolower($key), $protected)) {
                return true;
            }
        }
        return false;
    }
}

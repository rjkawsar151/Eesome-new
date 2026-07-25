<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Primary key auto-incrementing integer.
     */
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'detailed_role',
        'profile_pic',
        'is_verified',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_verified' => 'boolean',
    ];

    // Role helpers
    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super admin', 'manager']);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super admin';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'user_id');
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class, 'user_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'user_id');
    }

    public function otpCodes()
    {
        return $this->hasMany(OtpCode::class, 'user_id');
    }
}

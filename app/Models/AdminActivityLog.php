<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminActivityLog extends Model
{
    protected $fillable = ['admin_id', 'action', 'subject_type', 'subject_id', 'description', 'new_values', 'ip_address', 'user_agent'];

    protected $casts = ['new_values' => 'array'];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}

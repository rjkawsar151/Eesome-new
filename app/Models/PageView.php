<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageView extends Model
{
    protected $table = 'page_views';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'ip_address', 'url', 'user_agent', 'user_id', 'referrer', 'source',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

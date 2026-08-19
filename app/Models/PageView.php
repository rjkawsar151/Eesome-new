<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;

class PageView extends Model
{
    use MassPrunable;

    protected $table = 'page_views';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'ip_address', 'url', 'user_agent', 'user_id', 'referrer', 'source',
    ];

    /**
     * Get the prunable model query (retains maximum 60 days of visitor data).
     */
    public function prunable(): Builder
    {
        return static::where('created_at', '<=', now()->subDays(60));
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

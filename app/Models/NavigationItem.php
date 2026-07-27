<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NavigationItem extends Model
{
    protected $fillable = ['location', 'label', 'url', 'open_in_new_tab', 'is_active', 'sort_order'];

    protected $casts = ['open_in_new_tab' => 'boolean', 'is_active' => 'boolean'];
}

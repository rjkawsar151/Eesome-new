<?php

namespace App\Services;

use App\Models\NavigationItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class NavigationRepository
{
    public function for(string $location): Collection
    {
        return Cache::remember("navigation.{$location}", 3600, fn () => NavigationItem::where('location', $location)->where('is_active', true)->orderBy('sort_order')->get());
    }

    public function clear(): void
    {
        Cache::forget('navigation.header');
        Cache::forget('navigation.footer');
    }
}

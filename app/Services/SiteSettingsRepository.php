<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;

class SiteSettingsRepository
{
    private const CACHE_KEY = 'site_settings_safe';
    private const CACHE_TTL = 600; // 10 minutes

    public function all(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            try {
                return SiteSetting::all()
                    ->reject(fn($s) => SiteSetting::isProtectedKey($s->setting_key))
                    ->pluck('setting_value', 'setting_key')
                    ->toArray();
            } catch (\Throwable $e) {
                return [];
            }
        });
    }

    public function get(string $key, mixed $default = null): mixed
    {
        if (SiteSetting::isProtectedKey($key)) {
            return $default;
        }
        $settings = $this->all();
        return $settings[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        if (SiteSetting::isProtectedKey($key)) {
            throw new \RuntimeException("Cannot store protected key '{$key}' in site_settings.");
        }
        SiteSetting::updateOrCreate(
            ['setting_key' => $key],
            ['setting_value' => $value]
        );
        Cache::forget(self::CACHE_KEY);
    }

    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}

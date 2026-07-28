<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\OptimizedImageStorage;
use App\Services\SiteSettingsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    private const KEYS = ['store_name', 'store_tagline', 'contact_email', 'support_email', 'contact_phone', 'contact_whatsapp', 'business_address', 'logo_path', 'currency_symbol', 'shipping_default_charge', 'shipping_free_threshold', 'cod_enabled', 'facebook_url', 'instagram_url', 'default_meta_title', 'default_meta_description'];

    public function edit(SiteSettingsRepository $s)
    {
        $values = [];
        foreach (self::KEYS as $k) {
            $values[$k] = $s->get($k);
        }

return view('admin.settings.edit', compact('values'));
    }

    public function update(Request $r, SiteSettingsRepository $s)
    {
        $data = $r->validate(['store_name' => 'nullable|string|max:255', 'store_tagline' => 'nullable|string|max:255', 'contact_email' => 'nullable|email', 'support_email' => 'nullable|email', 'contact_phone' => 'nullable|string|max:50', 'contact_whatsapp' => 'nullable|string|max:50', 'business_address' => 'nullable|string|max:1000', 'currency_symbol' => 'nullable|string|max:10', 'shipping_default_charge' => 'nullable|numeric|min:0', 'shipping_free_threshold' => 'nullable|numeric|min:0', 'cod_enabled' => 'nullable|boolean', 'facebook_url' => 'nullable|url', 'instagram_url' => 'nullable|url', 'default_meta_title' => 'nullable|string|max:255', 'default_meta_description' => 'nullable|string|max:1000', 'logo_upload' => 'nullable|image|mimes:png,webp,jpg,jpeg|max:2048']);
        if ($logo = $r->file('logo_upload')) {
            $oldLogo = $s->get('logo_path');
            $data['logo_path'] = app(OptimizedImageStorage::class)->store($logo, 'branding', 900, 78);
            if (is_string($oldLogo) && str_starts_with($oldLogo, 'branding/')) {
                app(OptimizedImageStorage::class)->delete($oldLogo);
            }
        }
        unset($data['logo_upload']);
        $data['cod_enabled'] = $r->boolean('cod_enabled') ? '1' : '0';
        foreach ($data as $k => $v) {
            if (in_array($k, self::KEYS, true)) {
                $s->set($k, $v);
            }
        }

return back()->with('success', 'Settings saved.');
    }
}

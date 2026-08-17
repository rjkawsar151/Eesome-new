@extends('layouts.admin')
@section('title', 'Settings & Tracking')
@section('heading', 'Settings')

@push('styles')
<style>
.settings-section {
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid var(--line);
}
.settings-section:last-child {
    border-bottom: 0;
    margin-bottom: 0;
    padding-bottom: 0;
}
.settings-section-title {
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--ink);
    margin: 0 0 .35rem;
    display: flex;
    align-items: center;
    gap: .5rem;
}
.settings-section-desc {
    font-size: .85rem;
    color: var(--muted);
    margin: 0 0 1.25rem;
}
.code-textarea {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    font-size: .85rem;
    line-height: 1.4;
    background: #f8fafc;
}
</style>
@endpush

@section('content')
<div class="page-head">
    <div>
        <h1 class="title">Store &amp; Tracking Settings</h1>
        <p class="subtle">Manage general store information, Meta Pixel / CAPI, Google Tag Manager, GA4, Ads, and custom scripts. All settings are loaded directly from the database.</p>
    </div>
</div>

<form class="card" method="POST" enctype="multipart/form-data" action="{{ route('admin.settings.update') }}">
    @csrf
    @method('PUT')

    {{-- Section 1: General Store Details --}}
    <div class="settings-section">
        <h2 class="settings-section-title">Store Profile &amp; Contact</h2>
        <p class="settings-section-desc">Basic storefront branding, address, and support contacts.</p>

        <div class="form-grid">
            @foreach([
                'store_name' => 'Store Name',
                'store_tagline' => 'Store Tagline',
                'contact_email' => 'Contact Email',
                'support_email' => 'Support Email',
                'contact_phone' => 'Contact Phone',
                'contact_whatsapp' => 'WhatsApp Number',
                'currency_symbol' => 'Currency Symbol',
                'facebook_url' => 'Facebook Page URL',
                'instagram_url' => 'Instagram URL',
                'default_meta_title' => 'Default SEO Title'
            ] as $k => $l)
                <div class="field">
                    <label>{{ $l }}</label>
                    <input class="input" name="{{ $k }}" value="{{ old($k, $values[$k] ?? '') }}">
                </div>
            @endforeach

            <div class="field">
                <label>Store Logo</label>
                <input class="input" type="file" name="logo_upload" accept="image/png,image/webp,image/jpeg">
                <small class="subtle">PNG, WebP, or JPG. Maximum 2MB.</small>
                @if(!empty($values['logo_path']))
                    <div style="margin-top:.5rem;padding:.5rem;background:#f8fafc;border-radius:6px;display:inline-block">
                        <img src="{{ asset('storage/'.$values['logo_path']) }}" alt="Store logo" style="max-width:160px;max-height:60px;object-fit:contain;display:block">
                    </div>
                @endif
            </div>

            <div class="field full">
                <label>Business / Store Address</label>
                <textarea class="textarea" name="business_address" rows="2">{{ old('business_address', $values['business_address'] ?? '') }}</textarea>
            </div>

            <div class="field full">
                <label>Default SEO Meta Description</label>
                <textarea class="textarea" name="default_meta_description" rows="2">{{ old('default_meta_description', $values['default_meta_description'] ?? '') }}</textarea>
            </div>
        </div>
    </div>

    {{-- Section 2: Checkout & Shipping --}}
    <div class="settings-section">
        <h2 class="settings-section-title">Shipping &amp; Checkout</h2>
        <p class="settings-section-desc">Default shipping charges and payment options.</p>

        <div class="form-grid">
            <div class="field">
                <label>Default Shipping Charge (BDT)</label>
                <input class="input" type="number" step="0.01" name="shipping_default_charge" value="{{ old('shipping_default_charge', $values['shipping_default_charge'] ?? '') }}">
            </div>
            <div class="field">
                <label>Free Shipping Threshold (BDT)</label>
                <input class="input" type="number" step="0.01" name="shipping_free_threshold" value="{{ old('shipping_free_threshold', $values['shipping_free_threshold'] ?? '') }}">
            </div>
            <div class="field full">
                <label style="display:flex;align-items:center;gap:.5rem;font-weight:600;cursor:pointer">
                    <input type="checkbox" name="cod_enabled" value="1" @checked(($values['cod_enabled'] ?? '1') === '1')>
                    <span>Enable Cash on Delivery (COD) payment method</span>
                </label>
            </div>
        </div>
    </div>

    {{-- Section 3: Meta / Facebook Pixel & CAPI --}}
    <div class="settings-section">
        <h2 class="settings-section-title">
            <span>Meta / Facebook Tracking &amp; Conversions API</span>
        </h2>
        <p class="settings-section-desc">Configure your Meta Pixel and server-side Conversions API (CAPI) for accurate event tracking and deduplication.</p>

        <div class="form-grid">
            <div class="field">
                <label>Meta Pixel ID</label>
                <input class="input" name="meta_pixel_id" value="{{ old('meta_pixel_id', $values['meta_pixel_id'] ?? '') }}" placeholder="e.g. 1598500547854347">
                <small class="subtle">Used for frontend browser Pixel events.</small>
            </div>

            <div class="field">
                <label>Meta Test Event Code (Optional)</label>
                <input class="input" name="meta_test_event_code" value="{{ old('meta_test_event_code', $values['meta_test_event_code'] ?? '') }}" placeholder="e.g. TEST12345">
                <small class="subtle">Only enter when testing server events in Meta Events Manager.</small>
            </div>

            <div class="field full">
                <label>Meta CAPI Access Token (Server-Side)</label>
                <textarea class="textarea code-textarea" name="meta_capi_token" rows="3" placeholder="EAA... (Never exposed to browser)">{{ old('meta_capi_token', $values['meta_capi_token'] ?? '') }}</textarea>
                <small class="subtle">Long-lived access token from Meta Business Manager. Processed strictly server-side.</small>
            </div>
        </div>
    </div>

    {{-- Section 4: Google Tracking & Ads --}}
    <div class="settings-section">
        <h2 class="settings-section-title">Google Tracking &amp; Analytics</h2>
        <p class="settings-section-desc">Google Tag Manager, Google Analytics 4 (GA4), and Google Ads conversion tracking.</p>

        <div class="form-grid">
            <div class="field">
                <label>Google Tag Manager (GTM) Container ID</label>
                <input class="input" name="google_gtm_id" value="{{ old('google_gtm_id', $values['google_gtm_id'] ?? '') }}" placeholder="e.g. GTM-5FK7CHXW">
            </div>

            <div class="field">
                <label>Google Analytics (GA4) Measurement ID</label>
                <input class="input" name="google_analytics_id" value="{{ old('google_analytics_id', $values['google_analytics_id'] ?? '') }}" placeholder="e.g. G-XXXXXXXXXX">
            </div>

            <div class="field">
                <label>Google Tag ID (gtag)</label>
                <input class="input" name="google_tag_id" value="{{ old('google_tag_id', $values['google_tag_id'] ?? '') }}" placeholder="e.g. GT-XXXXXXXXXX">
            </div>

            <div class="field">
                <label>Google Ads ID</label>
                <input class="input" name="google_ads_id" value="{{ old('google_ads_id', $values['google_ads_id'] ?? '') }}" placeholder="e.g. AW-XXXXXXXXXX">
            </div>

            <div class="field">
                <label>Google Ads Conversion ID</label>
                <input class="input" name="google_ads_conversion_id" value="{{ old('google_ads_conversion_id', $values['google_ads_conversion_id'] ?? '') }}" placeholder="e.g. 123456789">
            </div>

            <div class="field">
                <label>Google Ads Conversion Label</label>
                <input class="input" name="google_ads_conversion_label" value="{{ old('google_ads_conversion_label', $values['google_ads_conversion_label'] ?? '') }}" placeholder="e.g. abcdEFGHijk">
            </div>
        </div>
    </div>

    {{-- Section 5: Custom Code & Script Snippets --}}
    <div class="settings-section">
        <h2 class="settings-section-title">Custom Scripts &amp; Snippets</h2>
        <p class="settings-section-desc">Inject custom HTML, verification tags, or JavaScript directly into the page head or body from the database.</p>

        <div class="form-grid">
            <div class="field full">
                <label>Header Scripts (Injected inside &lt;head&gt;)</label>
                <textarea class="textarea code-textarea" name="custom_header_scripts" rows="4" placeholder="<!-- Custom verification tags, meta tags, or analytics scripts -->">{{ old('custom_header_scripts', $values['custom_header_scripts'] ?? '') }}</textarea>
                <small class="subtle">Added to the &lt;head&gt; section across all storefront pages.</small>
            </div>

            <div class="field full">
                <label>Footer / Body Scripts (Injected before &lt;/body&gt;)</label>
                <textarea class="textarea code-textarea" name="custom_footer_scripts" rows="4" placeholder="<!-- Custom chat widgets, conversion tracking pixels, or body scripts -->">{{ old('custom_footer_scripts', $values['custom_footer_scripts'] ?? '') }}</textarea>
                <small class="subtle">Added at the bottom of the page before the closing &lt;/body&gt; tag.</small>
            </div>
        </div>
    </div>

    <div style="margin-top:1.5rem">
        <button class="btn btn-primary btn-lg" style="padding:.75rem 2rem">Save All Settings</button>
    </div>
</form>
@endsection
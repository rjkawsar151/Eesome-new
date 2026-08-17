<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Meta / Facebook Tracking & Conversions API (CAPI) Configuration
    |--------------------------------------------------------------------------
    |
    | Centralized configuration for Meta Pixel and server-side Conversions API.
    | Sensitive tokens (such as META_APP_SECRET and META_CAPI_TOKEN) must
    | remain server-side only and never be exposed in client-side code.
    |
    */
    'meta' => [
        'pixel_id' => env('META_PIXEL_ID'),
        'dataset_id' => env('META_DATASET_ID'),
        'app_id' => env('META_APP_ID'),
        'app_secret' => env('META_APP_SECRET'),
        'capi_token' => env('META_CAPI_TOKEN', env('META_ACCESS_TOKEN')),
        'access_token' => env('META_ACCESS_TOKEN', env('META_CAPI_TOKEN')),
        'test_event_code' => env('META_TEST_EVENT_CODE'),
        'api_version' => env('META_API_VERSION', 'v20.0'),
        'page_id' => env('META_PAGE_ID'),
        'business_id' => env('META_BUSINESS_ID'),
        'ad_account_id' => env('META_AD_ACCOUNT_ID'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google Tracking, Analytics & Advertising Configuration
    |--------------------------------------------------------------------------
    |
    | Centralized configuration for Google Tag Manager, Google Analytics (GA4),
    | Google Tag (gtag.js), and Google Ads conversion tracking.
    |
    */
    'google' => [
        'gtm_id' => env('GOOGLE_GTM_ID'),
        'analytics_id' => env('GOOGLE_ANALYTICS_ID'),
        'tag_id' => env('GOOGLE_TAG_ID'),
        'ads_id' => env('GOOGLE_ADS_ID'),
        'conversion_id' => env('GOOGLE_ADS_CONVERSION_ID'),
        'conversion_label' => env('GOOGLE_ADS_CONVERSION_LABEL'),
    ],

];

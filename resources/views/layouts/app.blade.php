<!DOCTYPE html>
<html lang="en">
<head>
    @php
        $siteSettings = app(\App\Services\SiteSettingsRepository::class);
        $gtmId = $siteSettings->get('google_gtm_id') ?: config('tracking.google.gtm_id');
        $gaId = $siteSettings->get('google_analytics_id') ?: ($siteSettings->get('google_tag_id') ?: (config('tracking.google.analytics_id') ?: config('tracking.google.tag_id')));
        $pixelId = $siteSettings->get('meta_pixel_id') ?: (config('tracking.meta.pixel_id') ?: config('services.meta.pixel_id'));
    @endphp

    @if($gtmId)
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','{{ $gtmId }}');</script>
    <!-- End Google Tag Manager -->
    @endif

    @if($gaId)
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '{{ $gaId }}');
    </script>
    @endif

    @if($pixelId)
    <!-- Meta Pixel Code -->
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '{{ $pixelId }}');
    fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id={{ $pixelId }}&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Meta Pixel Code -->
    @endif

    @if($headerScripts = $siteSettings->get('custom_header_scripts'))
    {!! $headerScripts !!}
    @endif
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'EEsome') | EEsome — Women's Handbags</title>
    <meta name="description" content="@yield('meta_description', 'Shop premium women\'s handbags at EEsome. Discover featured collections, new arrivals, and exclusive designs.')">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @php
        $faviconLogoPath = app(\App\Services\SiteSettingsRepository::class)->get('logo_path');
    @endphp
    @if($faviconLogoPath)
    <link rel="apple-touch-icon" href="{{ asset('storage/'.$faviconLogoPath) }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --brand-50: #fdf2f8;
            --brand-100: #fce7f3;
            --brand-400: #f472b6;
            --brand-600: #db2777;
            --brand-700: #be185d;
            --brand-900: #831843;
            --surface: #ffffff;
            --surface-alt: #fdf2f8;
            --text-primary: #1a1a2e;
            --text-muted: #6b7280;
        }
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { min-height: 100vh; display: flex; flex-direction: column; font-family: 'Outfit', sans-serif; background: var(--surface); color: var(--text-primary); margin: 0; padding-bottom: 0; -webkit-font-smoothing: antialiased; }
        ::selection { background: var(--brand-100); color: var(--brand-900); }
        :focus-visible { outline: 3px solid var(--brand-400); outline-offset: 3px; }

        /* ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ Navigation ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ */
        .nav { position: sticky; top: 0; z-index: 100; background: rgba(26,10,46,.88); -webkit-backdrop-filter: blur(22px) saturate(145%); backdrop-filter: blur(22px) saturate(145%); border-bottom: 1px solid rgba(167,139,250,.18); box-shadow: 0 8px 28px rgba(10,4,22,.28); }
        .nav-inner { position: relative; width: min(calc(100% - 40px), 1200px); min-height: 64px; margin-inline: auto; display: flex; align-items: center; justify-content: space-between; gap: 1rem; }
        .nav-logo img { display: block; width: clamp(150px, 14vw, 170px); height: 44px; object-fit: contain; }
        .nav-logo { flex-shrink: 0; font-family: Georgia, 'Times New Roman', serif; font-size: clamp(1.5rem, 1.8vw, 1.65rem); font-weight: 700; letter-spacing: -.04em; background: linear-gradient(135deg, var(--brand-900), var(--brand-400)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; text-decoration: none; }
        .nav-links { position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); display: flex; align-items: center; gap: clamp(1.15rem, 1.7vw, 1.65rem); list-style: none; margin: 0; padding: 0; white-space: nowrap; max-width: 100%; }
        .nav-links a { text-decoration: none; color: #f9eaf3; font-weight: 700; font-size: clamp(.92rem, 1.05vw, 1.02rem); text-transform: uppercase; letter-spacing: .06em; transition: color .2s; line-height: 1; }
        .nav-links a:hover { color: var(--brand-400); }
        .nav-actions { display: flex; gap: .75rem; align-items: center; flex-shrink: 0; }
        .nav-search { display: flex; align-items: center; position: relative; margin-left: auto; flex-shrink: 0; }
        .nav-search input { width: clamp(150px, 15vw, 195px); height: 36px; padding: 0 2.15rem 0 .8rem; border: 1px solid rgba(255,255,255,.22); border-radius: 999px; background: rgba(255,255,255,.12); color: #fff; font: inherit; font-size: .78rem; }
        .nav-search input::placeholder { color: rgba(255,255,255,.68); }
        .nav-search button { position: absolute; right: .3rem; width: 29px; height: 29px; padding: 0; border: 0; border-radius: 50%; background: transparent; color: #f9a8d4; cursor: pointer; display: grid; place-items: center; }
        .nav-search button svg { width: 16px; height: 16px; }
        .nav-btn { padding: .45rem 1rem; border-radius: 999px; font-size: .78rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: all .2s; }
        .nav-btn-ghost { border: 1.5px solid rgba(249,168,212,.8); color: #fce7f3; background: rgba(255,255,255,.06); }
        .nav-btn-ghost:hover { background: var(--brand-600); color: #fff; }
        .nav-btn-fill { background: var(--brand-600); color: #fff; border: none; }
        .nav-btn-fill:hover { background: var(--brand-700); }
        .nav-cart { position: relative; display: flex; align-items: center; margin-right: .25rem; color: #fff; }
        .nav-cart svg { width: 21px; height: 21px; }
        .nav-login { color: #f9eaf3; text-decoration: none; font-size: .82rem; font-weight: 600; padding: .45rem .2rem; }
        .nav-login:hover { color: var(--brand-400); }
        .nav-cart-badge { position: absolute; top: -8px; right: -8px; background: var(--brand-600); color: #fff; border-radius: 50%; width: 18px; height: 18px; font-size: 0.65rem; display: flex; align-items: center; justify-content: center; font-weight: 700; }
        .mobile-bottom-nav { display: none; }

        /* ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ WhatsApp Button ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ */
        .whatsapp-btn { position: fixed; bottom: calc(1rem + env(safe-area-inset-bottom)); right: 1rem; z-index: 200; background: #25d366; color: #fff; border-radius: 50%; width: 58px; height: 58px; display: flex; align-items: center; justify-content: center; text-decoration: none; box-shadow: 0 4px 20px rgba(37,211,102,0.4); transition: transform .2s, box-shadow .2s; }
        .whatsapp-btn:hover { transform: scale(1.1); box-shadow: 0 6px 28px rgba(37,211,102,0.5); }
        .whatsapp-btn::before { content: 'Chat with us'; position: absolute; right: calc(100% + .65rem); padding: .45rem .7rem; border-radius: 8px; background: #17121a; color: #fff; font-size: .75rem; font-weight: 600; white-space: nowrap; opacity: 0; transform: translateX(6px); pointer-events: none; transition: opacity .2s, transform .2s; }
        .whatsapp-btn:hover::before, .whatsapp-btn:focus-visible::before { opacity: 1; transform: translateX(0); }

        /* ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ Alerts ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ */
        .alert { padding: .75rem 1.25rem; border-radius: 10px; margin-bottom: 1rem; font-size: .9rem; }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-error   { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .toast-success { position: fixed; z-index: 500; top: 88px; right: 1.25rem; display: flex; align-items: center; gap: .75rem; max-width: min(390px, calc(100vw - 2rem)); padding: .9rem 1rem; border: 1px solid rgba(255,255,255,.32); border-radius: 14px; background: rgba(22,101,52,.94); color: #fff; box-shadow: 0 14px 35px rgba(0,0,0,.24); backdrop-filter: blur(14px); transition: opacity .25s, transform .25s; }
        .toast-success.is-leaving { opacity: 0; transform: translateY(-10px); }
        .toast-success button { margin-left: auto; border: 0; background: transparent; color: #fff; font-size: 1.15rem; cursor: pointer; }

        /* ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ Container ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ */
        .container { width: min(calc(100% - 40px), 1200px); margin-inline: auto; }
        .section-gap { padding: 4rem 0; }

        /* ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ Footer ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ */
        .site-help { width: 100%; padding: 4rem 1.5rem; margin-top: 5rem; background: radial-gradient(circle at top right, rgba(219,39,119,.28), transparent 42%), linear-gradient(135deg, #241921, #17121a); color: #fff; border-top: 1px solid rgba(249,168,212,.2); }
        .site-help__inner { max-width: 820px; margin: 0 auto; text-align: center; }
        .site-help__eyebrow { display: inline-block; margin-bottom: .8rem; color: #f9a8d4; font-size: .75rem; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; }
        .site-help h2 { margin: 0 0 .75rem; font-family: Georgia, 'Times New Roman', serif; font-size: clamp(1.8rem, 4vw, 2.8rem); font-weight: 500; }
        .site-help p { max-width: 620px; margin: 0 auto 1.5rem; color: #d1c4cd; line-height: 1.7; }
        .site-help__button { display: inline-flex; align-items: center; gap: .65rem; padding: .85rem 1.35rem; border-radius: 999px; background: #25d366; color: #fff; text-decoration: none; font-weight: 800; box-shadow: 0 10px 30px rgba(37,211,102,.24); transition: transform .2s, box-shadow .2s; }
        .site-help__button:hover { color: #fff; transform: translateY(-2px); box-shadow: 0 14px 34px rgba(37,211,102,.36); }
        footer { background: #17121a; color: #d1d5db; padding: 4rem 0 1.5rem; margin-top: auto; }
        footer h4 { color: #fff; margin: 0 0 1rem; font-size: 1rem; }
        footer a { color: #9ca3af; text-decoration: none; }
        footer a:hover { color: var(--brand-400); }
        footer .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 2rem; }
        footer .footer-bottom { border-top: 1px solid #374151; padding-top: 1.5rem; margin-top: 2rem; text-align: center; font-size: .8rem; color: #6b7280; }
        footer .footer-credit-link { color: var(--brand-400); font-weight: 800; text-decoration: underline; text-underline-offset: 3px; }
        footer .footer-credit-link:hover { color: #fff; }
        @media (max-width: 768px) {
            body { padding-bottom: calc(86px + env(safe-area-inset-bottom)); }
            .nav-links { display: none; }
            .nav { background: #e1d0f0; -webkit-backdrop-filter: none; backdrop-filter: none; border-bottom: 1px solid rgba(190,24,93,.15); box-shadow: 0 4px 16px rgba(0,0,0,.06); animation: mobileHeaderEnter .3s cubic-bezier(.22,1,.36,1) both; }
            .nav-inner { width: min(calc(100% - 24px), 1200px); min-height: 62px; gap: .75rem; }
            .nav-logo { font-size: 1.55rem; background: linear-gradient(135deg, #831843, #be185d); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
            .nav-logo img { max-width: clamp(88px,28vw,120px); height: 42px; }
            .nav-search { display: flex; flex: 1 1 auto; min-width: 0; margin-left: 0; }
            .nav-search input { width: 100%; min-width: 0; height: 40px; padding-left: .8rem; background: #ffffff; color: #1e1b4b; border: 1px solid rgba(0,0,0,.14); }
            .nav-search input::placeholder { color: #6b7280; }
            .nav-search button { color: #831843; }
            .nav-search input:focus { border-color: rgba(219,39,119,.9); outline: 2px solid rgba(219,39,119,.25); outline-offset: 1px; }
            .nav-actions { display: none; }
            footer { background: #17121a; color: #d1d5db; }
            footer h4 { color: #fff; }
            footer a { color: #9ca3af; }
            footer a:hover { color: var(--brand-400); }
            footer p { color: #9ca3af; }
            footer .footer-brand-title { color: #fff !important; }
            footer .footer-grid { grid-template-columns: 1fr 1fr; }
            footer .footer-bottom { border-top: 1px solid #374151; color: #6b7280; }
            footer .footer-credit-link { color: var(--brand-400); }
            .whatsapp-btn { bottom: calc(92px + env(safe-area-inset-bottom)); width: 52px; height: 52px; }
            .mobile-bottom-nav { position: fixed; z-index: 1000; right: 1rem; bottom: calc(.75rem + env(safe-area-inset-bottom)); left: 1rem; display: grid; grid-template-columns: repeat(3,minmax(0,1fr)); max-width: 420px; min-height: 64px; margin-inline: auto; padding: .4rem; border: 1px solid rgba(190,24,93,.2); border-radius: 20px; background: #e1d0f0; box-shadow: 0 10px 32px rgba(10,4,22,.16); -webkit-backdrop-filter: blur(16px) saturate(135%); backdrop-filter: blur(16px) saturate(135%); animation: mobileBottomNavEnter .36s cubic-bezier(.22,1,.36,1) both; }
            .mobile-bottom-nav__item { position: relative; display: grid; width: 100%; min-width: 44px; min-height: 52px; place-items: center; border-radius: 15px; color: #4b5563; text-decoration: none; transition: color .2s ease,background-color .2s ease,transform .2s ease; }
            .mobile-bottom-nav__item svg { width: 23px; height: 23px; fill: none; stroke: currentColor; stroke-width: 1.9; stroke-linecap: round; stroke-linejoin: round; }
            .mobile-bottom-nav__item[aria-current=page] { background: rgba(255,255,255,.65); color: #831843; }
            .mobile-bottom-nav__item:active { transform: scale(.94); }
            .mobile-bottom-nav__badge { position: absolute; top: 5px; left: calc(50% + 7px); display: grid; min-width: 17px; height: 17px; padding: 0 4px; place-items: center; border: 2px solid #e1d0f0; border-radius: 999px; background: var(--brand-600); color: #fff; font-size: 9px; font-weight: 800; line-height: 1; }
            @keyframes mobileHeaderEnter { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: translateY(0); } }
            @keyframes mobileBottomNavEnter { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: translateY(0); } }
        }
        @media (min-width:769px) and (max-width:1100px) {
            .nav-links { position: static; transform: none; margin-inline: auto; }
            .nav-links li:nth-child(n+4) { display: none; }
            .nav-search input { width: clamp(140px, 18vw, 175px); }
            .nav-login { display: none; }
        }
        @media (hover:hover) and (max-width:768px) { .mobile-bottom-nav__item:hover { color: #831843; background: rgba(255,255,255,.4); transform: translateY(-2px) scale(1.03); } }
        @supports not ((backdrop-filter: blur(16px)) or (-webkit-backdrop-filter: blur(16px))) { .mobile-bottom-nav { background: #e1d0f0; } }
        @media (prefers-reduced-motion: reduce) { .nav, .mobile-bottom-nav { animation: none; } .mobile-bottom-nav__item { transition: none; } .mobile-bottom-nav__item:active { transform: none; } }
    </style>
    @stack('styles')
</head>
<body>
@if($gtmId = ($siteSettings->get('google_gtm_id') ?: config('tracking.google.gtm_id')))
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtmId }}"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
@endif
{{-- Navigation --}}
@php
    $settings = app(\App\Services\SiteSettingsRepository::class);
    $storeName = $settings->get('store_name', 'EEsome');
    $logoPath = $settings->get('logo_path');
@endphp
<nav class="nav">
    <div class="nav-inner">
        <a href="{{ route('home') }}" class="nav-logo" aria-label="{{ $storeName }} home">@if($logoPath)<img src="{{ asset('storage/'.$logoPath) }}" alt="{{ $storeName }}">@else{{ $storeName }}@endif</a>
        @php
            $headerLinks = app(\App\Services\NavigationRepository::class)->for('header');
        @endphp
        <ul class="nav-links">
            @foreach($headerLinks as $link)<li><a href="{{ $link->url }}" @if($link->open_in_new_tab) target="_blank" rel="noopener noreferrer" @endif>{{ $link->label }}</a></li>@endforeach
        </ul>
        <form class="nav-search" method="GET" action="{{ route('products.index') }}" role="search">
            <input type="search" name="search" value="{{ request()->routeIs('products.index') ? request('search') : '' }}" placeholder="Search products" aria-label="Search products">
            <button type="submit" aria-label="Submit search"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg></button>
        </form>
        <div class="nav-actions">
            <a href="{{ route('cart.index') }}" class="nav-cart" aria-label="Shopping cart">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
                @php $cartCount = app(\App\Services\CartService::class)->cartCount(); @endphp
                @if($cartCount > 0)<span class="nav-cart-badge" aria-hidden="true">{{ $cartCount > 99 ? '99+' : $cartCount }}</span>@endif
            </a>
            @auth
                <a href="{{ route('dashboard') }}" class="nav-btn nav-btn-ghost">Account</a>
                <form method="POST" action="{{ route('logout') }}" style="display:inline">
                    @csrf
                    <button type="submit" class="nav-btn nav-btn-fill">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="nav-btn nav-btn-fill">Login</a>
            @endauth
        </div>
    </div>
</nav>

<nav class='mobile-bottom-nav' aria-label='Mobile navigation'>
    <a href='{{ route('products.index') }}' class='mobile-bottom-nav__item' aria-label='Shop' @if(request()->routeIs('products.*')) aria-current='page' @endif>
        <svg viewBox='0 0 24 24' aria-hidden='true' focusable='false'><path d='M3 10h18'/><path d='M5 10v10h14V10'/><path d='m4 10 2-6h12l2 6'/><path d='M9 20v-6h6v6'/></svg>
    </a>
    <a href='{{ route('cart.index') }}' class='mobile-bottom-nav__item' aria-label='View cart' @if(request()->routeIs('cart.*')) aria-current='page' @endif>
        <svg viewBox='0 0 24 24' aria-hidden='true' focusable='false'><path d='M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z'/><path d='M3 6h18'/><path d='M16 10a4 4 0 0 1-8 0'/></svg>
        @if($cartCount > 0)<span class='mobile-bottom-nav__badge' aria-label='{{ $cartCount }} items in cart'>{{ $cartCount > 99 ? '99+' : $cartCount }}</span>@endif
    </a>
    @auth
        <a href='{{ route('dashboard') }}' class='mobile-bottom-nav__item' aria-label='Open profile' @if(request()->routeIs('dashboard', 'profile.*')) aria-current='page' @endif>
            <svg viewBox='0 0 24 24' aria-hidden='true' focusable='false'><circle cx='12' cy='8' r='4'/><path d='M4 21a8 8 0 0 1 16 0'/></svg>
        </a>
    @else
        <a href='{{ route('login') }}' class='mobile-bottom-nav__item' aria-label='Log in' @if(request()->routeIs('login', 'password.*')) aria-current='page' @endif>
            <svg viewBox='0 0 24 24' aria-hidden='true' focusable='false'><circle cx='12' cy='8' r='4'/><path d='M4 21a8 8 0 0 1 16 0'/></svg>
        </a>
    @endauth
</nav>

{{-- Flash messages --}}
<div class="container" style="margin-top:1rem">
    @if(session('success'))
        <div id="success-toast" class="toast-success" role="status" aria-live="polite"><span>✓</span><span>{{ session('success') }}</span><button type="button" aria-label="Dismiss notification">×</button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif
</div>

{{-- Page content --}}
@isset($header)
<header style="background:#fff;border-bottom:1px solid rgba(190,24,93,.12)">
    <div class="container" style="padding:1.5rem 0">
        {{ $header }}
    </div>
</header>
@endisset
@yield('content')
@isset($slot)
    {{ $slot }}
@endisset

{{-- WhatsApp Button --}}
@php
    $settings = app(\App\Services\SiteSettingsRepository::class);
    $waNumber = $settings->get('contact_whatsapp', $settings->get('whatsapp_number', $settings->get('contact_phone', '')));
@endphp
@if(!empty($waNumber))
<a href="https://wa.me/{{ preg_replace('/\D/', '', $waNumber) }}?text={{ urlencode('Hi, I found you on EEsome!') }}"
   class="whatsapp-btn" target="_blank" rel="noopener noreferrer" aria-label="Chat on WhatsApp">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
</a>
@endif

@if(!empty($waNumber))
<section class="site-help" aria-labelledby="site-help-title">
    <div class="site-help__inner">
        <span class="site-help__eyebrow">Personal shopping assistance</span>
        <h2 id="site-help-title">Need help finding your perfect bag?</h2>
        <p>Chat with our team for product guidance, availability, and quick answers before you order.</p>
        <a class="site-help__button" href="https://wa.me/{{ preg_replace('/\D/', '', $waNumber) }}?text={{ urlencode('Hi, I would like help choosing a handbag.') }}" target="_blank" rel="noopener noreferrer">
            <span aria-hidden="true">WhatsApp</span><span>Chat with us</span>
        </a>
    </div>
</section>
@endif

{{-- Footer --}}
<footer>
    <div class="container">
        <div class="footer-grid">
            <div>
                <div style="margin-bottom:.75rem;">@if($logoPath)<img src="{{ asset('storage/'.$logoPath) }}" alt="{{ $storeName }}" style="display:block;max-width:200px;max-height:90px;object-fit:contain">@else<span class="footer-brand-title" style="font-size:1.5rem;font-weight:800;color:#fff">{{ $storeName }}</span>@endif</div>
                <p style="font-size:.85rem;line-height:1.7;color:#9ca3af">Premium women's handbags — crafted for every occasion.</p>
            </div>
            <div>
                <h4>Explore</h4>
                <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:.5rem;font-size:.875rem">
                    @foreach(app(\App\Services\NavigationRepository::class)->for('footer') as $link)<li><a href="{{ $link->url }}" @if($link->open_in_new_tab) target="_blank" rel="noopener noreferrer" @endif>{{ $link->label }}</a></li>@endforeach
                </ul>
            </div>
            <div>
                <h4>Account</h4>
                <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:.5rem;font-size:.875rem">
                    @auth
                        <li><a href="{{ route('dashboard') }}">{{ auth()->user()->isAdmin() ? 'Admin Dashboard' : 'My Profile' }}</a></li>
                    @else
                        <li><a href="{{ route('login') }}">Login</a></li>
                    @endauth
                    <li><a href="{{ route('cart.index') }}">My Cart</a></li>
                </ul>
            </div>
            <div>
                <h4>Contact</h4>
                @if(!empty($waNumber))
                    <p style="font-size:.875rem">Phone: {{ $waNumber }}</p>
                @endif
            </div>
        </div>
        <div class="footer-bottom">
            &copy; {{ date('Y') }} EESOME. Copyright protected. Crafted with care by <a href="https://www.linkedin.com/in/kawsar202/" target="_blank" rel="noopener noreferrer" style="text-decoration:none;color:inherit;font-weight:700;font-style:italic">KAWSAR</a>.
        </div>
    </div>
</footer>

<script>
(() => {
    const toast = document.getElementById('success-toast');
    if (toast) {
        const close = () => { toast.classList.add('is-leaving'); setTimeout(() => toast.remove(), 260); };
        toast.querySelector('button')?.addEventListener('click', close);
        setTimeout(close, 3500);
    }

    window.updateCartBadge = function(count) {
        const badgeText = count > 99 ? '99+' : count;

        const navCart = document.querySelector('.nav-cart');
        if (navCart) {
            let badge = navCart.querySelector('.nav-cart-badge');
            if (count > 0) {
                if (!badge) {
                    badge = document.createElement('span');
                    badge.className = 'nav-cart-badge';
                    badge.setAttribute('aria-hidden', 'true');
                    navCart.appendChild(badge);
                }
                badge.textContent = badgeText;
            } else if (badge) {
                badge.remove();
            }
        }

        const mobileCart = document.querySelector('.mobile-bottom-nav__item[aria-label="View cart"]');
        if (mobileCart) {
            let mBadge = mobileCart.querySelector('.mobile-bottom-nav__badge');
            if (count > 0) {
                if (!mBadge) {
                    mBadge = document.createElement('span');
                    mBadge.className = 'mobile-bottom-nav__badge';
                    mobileCart.appendChild(mBadge);
                }
                mBadge.setAttribute('aria-label', count + ' items in cart');
                mBadge.textContent = badgeText;
            } else if (mBadge) {
                mBadge.remove();
            }
        }
    };

    window.showToast = function(message, type = 'success') {
        let toast = document.getElementById('success-toast');
        if (!toast) {
            const container = document.querySelector('.container') || document.body;
            toast = document.createElement('div');
            toast.id = 'success-toast';
            toast.className = type === 'error' ? 'alert alert-error' : 'toast-success';
            toast.setAttribute('role', 'status');
            toast.setAttribute('aria-live', 'polite');
            container.prepend(toast);
        } else {
            toast.classList.remove('is-leaving');
            toast.className = type === 'error' ? 'alert alert-error' : 'toast-success';
        }
        toast.innerHTML = type === 'error'
            ? `<span>${message}</span>`
            : `<span>✓</span><span>${message}</span><button type="button" aria-label="Dismiss notification">×</button>`;

        toast.querySelector('button')?.addEventListener('click', () => {
            toast.classList.add('is-leaving');
            setTimeout(() => toast.remove(), 260);
        });
        clearTimeout(window._toastTimer);
        window._toastTimer = setTimeout(() => {
            toast.classList.add('is-leaving');
            setTimeout(() => toast.remove(), 260);
        }, 3500);
    };

    document.addEventListener('submit', async (event) => {
        if (event.defaultPrevented) return;

        const form = event.target;
        if (!form || !form.action) return;

        const isCartStore = form.action.includes('/cart') && !form.action.includes('/cart/');
        if (!isCartStore || form.method.toUpperCase() !== 'POST') return;

        const submitter = event.submitter;
        if (submitter && submitter.name === 'buy_now') return;

        event.preventDefault();

        const submitBtn = submitter || form.querySelector('button[type="submit"]');
        const originalText = submitBtn ? submitBtn.innerHTML : '';
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.7';
        }

        try {
            const formData = new FormData(form);
            if (submitter && submitter.name) {
                formData.append(submitter.name, submitter.value);
            }

            const clientEventId = 'atc_' + Date.now() + '_' + Math.random().toString(36).substring(2, 9);
            if (!formData.has('event_id')) {
                formData.append('event_id', clientEventId);
            }

            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });

            const data = await response.json();

            if (response.ok && data.success) {
                window.updateCartBadge(data.cart_count);
                window.showToast(data.message || 'Successfully added to cart.');

                if (typeof window.fbq === 'function') {
                    window.fbq('track', 'AddToCart', {
                        content_name: data.product_name,
                        content_ids: data.content_id ? [String(data.content_id)] : [],
                        content_type: 'product',
                        value: Number(data.value || 0),
                        currency: data.currency || 'BDT'
                    }, {
                        eventID: data.event_id || clientEventId
                    });
                }

                if (submitBtn) {
                    submitBtn.innerHTML = '✓ Added!';
                    setTimeout(() => {
                        submitBtn.innerHTML = originalText;
                    }, 1500);
                }
            } else {
                window.showToast(data.message || 'Could not add item to cart.', 'error');
            }
        } catch (err) {
            window.showToast('Something went wrong. Please try again.', 'error');
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.style.opacity = '';
            }
        }
    });
})();
@if($footerScripts = $siteSettings->get('custom_footer_scripts'))
{!! $footerScripts !!}
@endif
@stack('scripts')
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'EEsome') | EEsome – Women's Handbags</title>
    <meta name="description" content="@yield('meta_description', 'Shop premium women\'s handbags at EEsome. Discover featured collections, new arrivals, and exclusive designs.')">
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
        body { font-family: 'Outfit', sans-serif; background: var(--surface); color: var(--text-primary); margin: 0; padding-bottom: calc(88px + env(safe-area-inset-bottom)); -webkit-font-smoothing: antialiased; }
        ::selection { background: var(--brand-100); color: var(--brand-900); }
        :focus-visible { outline: 3px solid var(--brand-400); outline-offset: 3px; }

        /* ── Navigation ── */
        .nav { position: sticky; top: 0; z-index: 100; background: rgba(255,255,255,0.9); backdrop-filter: blur(18px); border-bottom: 1px solid #f3e6f0; }
        .nav-inner { max-width: 1280px; margin: 0 auto; padding: 0 1.5rem; display: flex; align-items: center; justify-content: space-between; height: 72px; }
        .nav-logo { font-family: Georgia, 'Times New Roman', serif; font-size: 1.85rem; font-weight: 700; letter-spacing: -.04em; background: linear-gradient(135deg, var(--brand-900), var(--brand-400)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; text-decoration: none; }
        .nav-links { display: flex; align-items: center; gap: 2rem; list-style: none; margin: 0; padding: 0; }
        .nav-links a { text-decoration: none; color: var(--text-primary); font-weight: 500; font-size: 0.9rem; transition: color .2s; }
        .nav-links a:hover { color: var(--brand-600); }
        .nav-actions { display: flex; gap: 1rem; align-items: center; }
        .nav-search { display: flex; align-items: center; position: relative; margin-left: auto; }
        .nav-search input { width: min(18vw, 220px); height: 40px; padding: 0 2.35rem 0 .85rem; border: 1px solid #eadde6; border-radius: 999px; background: #fff; font: inherit; font-size: .85rem; }
        .nav-search input:focus { border-color: var(--brand-400); outline: 3px solid var(--brand-100); }
        .nav-search button { position: absolute; right: .35rem; width: 32px; height: 32px; padding: 0; border: 0; border-radius: 50%; background: transparent; color: var(--brand-700); cursor: pointer; display: grid; place-items: center; }
        .nav-btn { padding: 0.5rem 1.25rem; border-radius: 999px; font-size: 0.85rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: all .2s; }
        .nav-btn-ghost { border: 1.5px solid var(--brand-600); color: var(--brand-600); background: transparent; }
        .nav-btn-ghost:hover { background: var(--brand-600); color: #fff; }
        .nav-btn-fill { background: var(--brand-600); color: #fff; border: none; }
        .nav-btn-fill:hover { background: var(--brand-700); }
        .nav-cart { position: relative; display: flex; align-items: center; margin-right: .5rem; color: var(--text-primary); }
        .nav-login { color: var(--text-primary); text-decoration: none; font-size: .9rem; font-weight: 600; padding: .5rem .25rem; }
        .nav-login:hover { color: var(--brand-600); }
        .nav-cart-badge { position: absolute; top: -8px; right: -8px; background: var(--brand-600); color: #fff; border-radius: 50%; width: 18px; height: 18px; font-size: 0.65rem; display: flex; align-items: center; justify-content: center; font-weight: 700; }

        /* ── WhatsApp Button ── */
        .whatsapp-btn { position: fixed; bottom: calc(1rem + env(safe-area-inset-bottom)); right: 1rem; z-index: 200; background: #25d366; color: #fff; border-radius: 50%; width: 58px; height: 58px; display: flex; align-items: center; justify-content: center; text-decoration: none; box-shadow: 0 4px 20px rgba(37,211,102,0.4); transition: transform .2s, box-shadow .2s; }
        .whatsapp-btn:hover { transform: scale(1.1); box-shadow: 0 6px 28px rgba(37,211,102,0.5); }
        .whatsapp-btn::before { content: 'Chat with us'; position: absolute; right: calc(100% + .65rem); padding: .45rem .7rem; border-radius: 8px; background: #17121a; color: #fff; font-size: .75rem; font-weight: 600; white-space: nowrap; opacity: 0; transform: translateX(6px); pointer-events: none; transition: opacity .2s, transform .2s; }
        .whatsapp-btn:hover::before, .whatsapp-btn:focus-visible::before { opacity: 1; transform: translateX(0); }

        /* ── Alerts ── */
        .alert { padding: .75rem 1.25rem; border-radius: 10px; margin-bottom: 1rem; font-size: .9rem; }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-error   { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }

        /* ── Container ── */
        .container { max-width: 1280px; margin: 0 auto; padding: 0 1.5rem; }
        .section-gap { padding: 4rem 0; }

        /* ── Footer ── */
        footer { background: #17121a; color: #d1d5db; padding: 4rem 0 1.5rem; margin-top: 5rem; }
        footer h4 { color: #fff; margin: 0 0 1rem; font-size: 1rem; }
        footer a { color: #9ca3af; text-decoration: none; }
        footer a:hover { color: var(--brand-400); }
        footer .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 2rem; }
        footer .footer-bottom { border-top: 1px solid #374151; padding-top: 1.5rem; margin-top: 2rem; text-align: center; font-size: .8rem; color: #6b7280; }
        @media (max-width: 768px) {
            footer .footer-grid { grid-template-columns: 1fr 1fr; }
            .nav-links { display: none; }
            .nav-search { display: none; }
            .nav-inner { height: 62px; padding: 0 1rem; }
            .nav-logo { font-size: 1.55rem; }
            .nav-actions { gap: .6rem; }
            .nav-btn { padding: .45rem .8rem; }
            .nav-login { display: none; }
            .whatsapp-btn { width: 52px; height: 52px; }
        }
    </style>
    @stack('styles')
</head>
<body>
{{-- Navigation --}}
<nav class="nav">
    <div class="nav-inner">
        <a href="{{ route('home') }}" class="nav-logo">EEsome</a>
        <ul class="nav-links">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><a href="{{ route('products.index') }}">Shop</a></li>
            <li><a href="#">Blog</a></li>
            <li><a href="{{ route('about') }}">About</a></li>
        </ul>
        <form class="nav-search" method="GET" action="{{ route('products.index') }}" role="search">
            <input type="search" name="search" value="{{ request()->routeIs('products.index') ? request('search') : '' }}" placeholder="Search bags, colors…" aria-label="Search products">
            <button type="submit" aria-label="Submit search"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg></button>
        </form>
        <div class="nav-actions">
            <a href="{{ route('cart.index') }}" class="nav-cart" aria-label="Shopping cart">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
                @php $cartCount = app(\App\Services\CartService::class)->cartCount(); @endphp
                @if($cartCount > 0)<span class="nav-cart-badge" aria-hidden="true">{{ $cartCount > 99 ? '99+' : $cartCount }}</span>@endif
            </a>
            @auth
                <a href="{{ route('profile.edit') }}" class="nav-btn nav-btn-ghost">Account</a>
                <form method="POST" action="{{ route('logout') }}" style="display:inline">
                    @csrf
                    <button type="submit" class="nav-btn nav-btn-fill">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="nav-login">Login</a>
                <a href="{{ route('register') }}" class="nav-btn nav-btn-fill">Register</a>
            @endauth
        </div>
    </div>
</nav>

{{-- Flash messages --}}
<div class="container" style="margin-top:1rem">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif
</div>

{{-- Page content --}}
@yield('content')

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

{{-- Footer --}}
<footer>
    <div class="container">
        <div class="footer-grid">
            <div>
                <div style="font-size:1.5rem;font-weight:800;color:#fff;margin-bottom:.75rem;">EEsome</div>
                <p style="font-size:.85rem;line-height:1.7;color:#9ca3af">Premium Women's Handbags — crafted for every occasion.</p>
            </div>
            <div>
                <h4>Shop</h4>
                <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:.5rem;font-size:.875rem">
                    <li><a href="{{ route('products.index') }}">All Products</a></li>
                    <li><a href="{{ route('products.index', ['new' => 1]) }}">New Arrivals</a></li>
                    <li><a href="{{ route('products.index', ['featured' => 1]) }}">Featured</a></li>
                </ul>
            </div>
            <div>
                <h4>Account</h4>
                <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:.5rem;font-size:.875rem">
                    @auth
                        <li><a href="{{ route('profile.edit') }}">My Profile</a></li>
                    @else
                        <li><a href="{{ route('login') }}">Login</a></li>
                        <li><a href="{{ route('register') }}">Register</a></li>
                    @endauth
                    <li><a href="{{ route('cart.index') }}">My Cart</a></li>
                </ul>
            </div>
            <div>
                <h4>Contact</h4>
                @if(!empty($waNumber))
                    <p style="font-size:.875rem">📱 {{ $waNumber }}</p>
                @endif
            </div>
        </div>
        <div class="footer-bottom">
            &copy; {{ date('Y') }} EEsome. All rights reserved.
        </div>
    </div>
</footer>

@stack('scripts')
</body>
</html>

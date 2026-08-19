<!DOCTYPE html>
<html lang="en">
<head>
    @php
        $gtmId = null;
        $storeName = 'EEsome';
        $logoPath = null;
        $waNumber = null;
        try {
            $siteSettings = app(\App\Services\SiteSettingsRepository::class);
            $gtmId = $siteSettings->get('google_gtm_id') ?: config('tracking.google.gtm_id');
            $storeName = $siteSettings->get('store_name', config('app.name', 'EEsome'));
            $logoPath = $siteSettings->get('logo_path');
            $waNumber = $siteSettings->get('contact_whatsapp', $siteSettings->get('whatsapp_number', $siteSettings->get('contact_phone', '')));
        } catch (\Throwable $e) {
            $gtmId = config('tracking.google.gtm_id');
        }
    @endphp

    @if($gtmId)
    <!-- Google Tag Manager -->
    <script>(function (w, d, s, l, i) {
            w[l] = w[l] || []; w[l].push({
                'gtm.start':
                    new Date().getTime(), event: 'gtm.js'
            }); var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : ''; j.async = true; j.src =
                    'https://www.googletagmanager.com/gtm.js?id=' + i + dl; f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', '{{ $gtmId }}');</script>
    <!-- End Google Tag Manager -->
    @endif

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>Server Error | {{ $storeName }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-warm: #FFFDFC;
            --blush-50: #FCECEF;
            --soft-pink: #F4B8C4;
            --rose-pink: #E9869C;
            --deep-rose: #C95875;
            --charcoal: #282426;
            --muted-text: #6E6468;
            --line-color: #F2E3E6;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            min-height: 100vh;
            background-color: var(--bg-warm);
            color: var(--charcoal);
            font-family: 'Outfit', system-ui, -apple-system, sans-serif;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
        }

        ::selection {
            background: var(--blush-50);
            color: var(--deep-rose);
        }

        .header {
            width: 100%;
            padding: 1.5rem 2.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 20;
        }

        .brand-logo {
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-size: 1.65rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: var(--charcoal);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .brand-logo img {
            max-height: 38px;
            width: auto;
            object-fit: contain;
        }

        .header-nav {
            display: flex;
            align-items: center;
            gap: 2rem;
            list-style: none;
        }

        .header-nav a {
            text-decoration: none;
            color: var(--muted-text);
            font-size: 0.82rem;
            font-weight: 500;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            transition: color 0.25s ease;
        }

        .header-nav a:hover {
            color: var(--deep-rose);
        }

        .hero-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 6rem 1.5rem 4rem;
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 1100px;
            margin: 0 auto;
            text-align: center;
        }

        .bg-aura {
            position: absolute;
            width: 520px;
            height: 520px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(252, 236, 239, 0.75) 0%, rgba(255, 253, 252, 0) 70%);
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
            z-index: -1;
        }

        .hero-composition {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 2.5rem;
            margin-bottom: 2rem;
            position: relative;
        }

        .luxury-tag {
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.28em;
            text-transform: uppercase;
            color: var(--deep-rose);
            margin-bottom: 0.75rem;
            display: inline-block;
        }

        .number-500 {
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-size: clamp(6.5rem, 14vw, 11rem);
            font-weight: 400;
            line-height: 0.9;
            color: var(--charcoal);
            letter-spacing: -0.03em;
            user-select: none;
        }

        .bag-art-wrapper {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            transition: transform 0.5s cubic-bezier(0.25, 1, 0.5, 1);
        }

        .bag-svg-container {
            width: 170px;
            height: 170px;
            position: relative;
            animation: floatBag 5s ease-in-out infinite;
        }

        .bag-svg {
            width: 100%;
            height: 100%;
            overflow: visible;
        }

        .sparkle-star {
            animation: sparklePulse 3s ease-in-out infinite;
        }

        .bag-shadow {
            width: 110px;
            height: 14px;
            background: radial-gradient(ellipse at center, rgba(201, 88, 117, 0.18) 0%, rgba(255, 253, 252, 0) 75%);
            border-radius: 50%;
            margin-top: 0.75rem;
            animation: shadowScale 5s ease-in-out infinite;
        }

        .copy-section {
            max-width: 580px;
            margin: 0 auto;
        }

        .main-headline {
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-size: clamp(1.75rem, 3.2vw, 2.5rem);
            font-weight: 500;
            color: var(--charcoal);
            line-height: 1.25;
            margin-bottom: 1rem;
        }

        .supporting-text {
            font-size: 1.02rem;
            color: var(--muted-text);
            line-height: 1.65;
            font-weight: 400;
            margin-bottom: 2.5rem;
        }

        .actions-group {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.25rem;
            flex-wrap: wrap;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: var(--deep-rose);
            color: #FFFFFF;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            padding: 1rem 2.2rem;
            border-radius: 8px;
            border: 1px solid var(--deep-rose);
            transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
            box-shadow: 0 4px 18px rgba(201, 88, 117, 0.18);
        }

        .btn-primary:hover {
            background-color: #B24662;
            border-color: #B24662;
            transform: translateY(-3px);
            box-shadow: 0 10px 28px rgba(201, 88, 117, 0.32);
        }

        .btn-whatsapp {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            justify-content: center;
            background-color: #25d366;
            color: #FFFFFF;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 1rem 1.8rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 18px rgba(37, 211, 102, 0.2);
        }

        .btn-whatsapp:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 28px rgba(37, 211, 102, 0.35);
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            color: var(--charcoal);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            letter-spacing: 0.04em;
            position: relative;
            padding-bottom: 4px;
            transition: color 0.25s ease;
        }

        .btn-secondary::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0%;
            height: 1.5px;
            background-color: var(--deep-rose);
            transition: width 0.3s cubic-bezier(0.25, 1, 0.5, 1);
        }

        .btn-secondary:hover {
            color: var(--deep-rose);
        }

        .btn-secondary:hover::after {
            width: 100%;
        }

        .footer-subtle {
            position: absolute;
            bottom: 1.5rem;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 0.78rem;
            color: #A49B9E;
            letter-spacing: 0.05em;
        }

        @keyframes floatBag {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-12px); }
        }

        @keyframes shadowScale {
            0%, 100% { transform: scale(1); opacity: 0.7; }
            50% { transform: scale(0.72); opacity: 0.35; }
        }

        @keyframes sparklePulse {
            0%, 100% { opacity: 0.2; transform: scale(0.8); }
            50% { opacity: 1; transform: scale(1.2); }
        }

        @media (max-width: 768px) {
            .header { padding: 1.25rem 1.5rem; }
            .header-nav { display: none; }
            .hero-composition { flex-direction: column; gap: 0.5rem; margin-bottom: 1.5rem; }
            .bag-svg-container { width: 140px; height: 140px; }
            .number-500 { font-size: 5.5rem; }
            .main-headline { font-size: 1.75rem; }
            .actions-group { flex-direction: column; width: 100%; gap: 1rem; }
            .btn-primary, .btn-whatsapp { width: 100%; }
        }
    </style>
</head>
<body>
    @if($gtmId)
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtmId }}" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    @endif

    <header class="header">
        <a href="{{ route('home') }}" class="brand-logo" aria-label="{{ $storeName }}">
            @if($logoPath)
                <img src="{{ asset('storage/'.$logoPath) }}" alt="{{ $storeName }}">
            @else
                EE<span style="color:var(--deep-rose)">some</span>
            @endif
        </a>
        <ul class="header-nav">
            <li><a href="{{ route('products.index') }}">Collections</a></li>
            <li><a href="{{ route('orders.track') }}">Track Order</a></li>
            <li><a href="{{ route('cart.index') }}">Bag</a></li>
        </ul>
    </header>

    <div class="bg-aura"></div>

    <main class="hero-container">
        <span class="luxury-tag">Temporary Glitch</span>

        <div class="hero-composition">
            <div class="number-500">500</div>

            <div class="bag-art-wrapper">
                <div class="bag-svg-container">
                    <svg class="bag-svg" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path class="sparkle-star" d="M32 40L34.5 47.5L42 50L34.5 52.5L32 60L29.5 52.5L22 50L29.5 47.5L32 40Z" fill="#F4B8C4" />
                        <path d="M68 92V66C68 48.3269 82.3269 34 100 34C117.673 34 132 48.3269 132 66V92" stroke="#282426" stroke-width="3" stroke-linecap="round"/>
                        <path d="M48 92H152L144 162C143.5 166.5 139.7 170 135.1 170H64.9C60.3 170 56.5 166.5 56 162L48 92Z" fill="#FFFDFC" stroke="#282426" stroke-width="3" stroke-linejoin="round"/>
                        <path d="M54 98H146L139.5 158C139.1 161.5 136.1 164 132.5 164H67.5C63.9 164 60.9 161.5 60.5 158L54 98Z" fill="#FCECEF" fill-opacity="0.6"/>
                        <path d="M48 92C48 92 78 126 100 126C122 126 152 92 152 92" stroke="#282426" stroke-width="2.5" stroke-linecap="round" fill="none"/>
                        <rect x="92" y="120" width="16" height="12" rx="3" fill="#E9869C" stroke="#282426" stroke-width="2"/>
                    </svg>
                </div>
                <div class="bag-shadow"></div>
            </div>
        </div>

        <div class="copy-section">
            <h1 class="main-headline">Something went wrong on our end.</h1>
            <p class="supporting-text">
                We're already looking into it. Please refresh the page, head back to our collections, or reach out on WhatsApp for instant assistance.
            </p>
        </div>

        <div class="actions-group">
            <a href="{{ route('home') }}" class="btn-primary">Return to Home</a>
            <a href="{{ route('products.index') }}" class="btn-secondary">Explore Handbags</a>
            @if(!empty($waNumber))
                <a href="https://wa.me/{{ preg_replace('/\D/', '', $waNumber) }}?text={{ urlencode('Hi, I experienced an issue while browsing EEsome.') }}" class="btn-whatsapp" target="_blank" rel="noopener noreferrer">
                    Chat on WhatsApp
                </a>
            @endif
        </div>
    </main>

    <div class="footer-subtle">
        &copy; {{ date('Y') }} {{ $storeName }}. All rights reserved.
    </div>
</body>
</html>

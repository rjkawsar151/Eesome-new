<!DOCTYPE html>
<html lang="en">
<head>
    @php
        $gtmId = null;
        $storeName = 'EEsome';
        $logoPath = null;
        try {
            $siteSettings = app(\App\Services\SiteSettingsRepository::class);
            $gtmId = $siteSettings->get('google_gtm_id') ?: config('tracking.google.gtm_id');
            $storeName = $siteSettings->get('store_name', config('app.name', 'EEsome'));
            $logoPath = $siteSettings->get('logo_path');
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
    <title>500 Server Error | {{ $storeName }}</title>
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

        /* Minimal Luxury Header */
        .header {
            width: 100%;
            padding: 1.5rem 2.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 20;
        }

        .footer-subtle {
            position: relative;
            margin-top: auto;
            padding: 1.75rem 1rem 2rem;
            width: 100%;
            text-align: center;
            font-size: 0.78rem;
            color: #A49B9E;
            letter-spacing: 0.05em;
            z-index: 10;
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

        /* Hero Container */
        .hero-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 4rem 1.5rem 2.5rem;
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 1100px;
            margin: 0 auto;
            text-align: center;
        }

        /* Background Subtle Decorative Elements */
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

        /* Hero Composition */
        .hero-composition {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 2.5rem;
            margin-bottom: 2rem;
            position: relative;
        }

        /* Label */
        .luxury-tag {
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.28em;
            text-transform: uppercase;
            color: var(--deep-rose);
            margin-bottom: 0.75rem;
            display: inline-block;
        }

        /* 500 Display Number */
        .number-404 {
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-size: clamp(6.5rem, 14vw, 11rem);
            font-weight: 400;
            line-height: 0.9;
            color: var(--charcoal);
            letter-spacing: -0.03em;
            user-select: none;
        }

        /* Text Copy Section */
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

        /* Action Buttons */
        .actions-group {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.75rem;
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
            padding: 1rem 2.4rem;
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

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .header {
                padding: 1.25rem 1.5rem;
            }
            .header-nav {
                display: none;
            }
            .hero-composition {
                flex-direction: column;
                gap: 0.5rem;
                margin-bottom: 1.5rem;
            }
            .number-404 {
                font-size: 5.5rem;
            }
            .main-headline {
                font-size: 1.75rem;
            }
            .actions-group {
                flex-direction: column;
                width: 100%;
                gap: 1.25rem;
            }
            .btn-primary {
                width: 100%;
                padding: 1.1rem 1.8rem;
            }
        }
    </style>
</head>
<body>
    @if($gtmId)
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtmId }}"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    @endif

    <!-- Minimal Header -->
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

    <!-- Background Decoration -->
    <div class="bg-aura"></div>

    <!-- Main Content -->
    <main class="hero-container">
        <span class="luxury-tag">Server Encountered an Issue</span>

        <div class="hero-composition">
            <div class="number-404">500</div>
        </div>

        <!-- Copy Text -->
        <div class="copy-section">
            <h1 class="main-headline">Something went unexpectedly wrong.</h1>
            <p class="supporting-text">
                Our servers experienced a temporary hitch while processing your request. Please try refreshing or return to our catalog.
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="actions-group">
            <a href="{{ route('products.index') }}" class="btn-primary">Back to Shop</a>
            <a href="{{ route('products.index') }}?sort=newest" class="btn-secondary">Explore New Arrivals</a>
        </div>
    </main>

    <!-- Footer Copyright -->
    <div class="footer-subtle">
        &copy; {{ date('Y') }} {{ $storeName }}. All rights reserved.
    </div>
</body>
</html>

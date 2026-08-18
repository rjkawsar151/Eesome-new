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
    <title>404 Page Not Found | {{ $storeName }}</title>
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

        /* Hero Container */
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

        .bg-strap-curve {
            position: absolute;
            top: 20%;
            right: 12%;
            width: 320px;
            height: 320px;
            border: 1px solid rgba(244, 184, 196, 0.25);
            border-radius: 50%;
            pointer-events: none;
            z-index: -1;
        }

        /* Main Hero Composition (404 Number + Handbag Art) */
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

        /* 404 Display Number */
        .number-404 {
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-size: clamp(6.5rem, 14vw, 11rem);
            font-weight: 400;
            line-height: 0.9;
            color: var(--charcoal);
            letter-spacing: -0.03em;
            user-select: none;
        }

        /* Animated Handbag Art Container */
        .bag-art-wrapper {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            padding: 1rem;
            transition: transform 0.5s cubic-bezier(0.25, 1, 0.5, 1);
        }

        .bag-art-wrapper:hover {
            transform: translateY(-8px) rotate(-2deg);
        }

        .bag-svg-container {
            width: 170px;
            height: 170px;
            position: relative;
            animation: floatBag 5s ease-in-out infinite;
        }

        /* SVG Bag Elements */
        .bag-svg {
            width: 100%;
            height: 100%;
            overflow: visible;
        }

        /* Hanging Charm Animation */
        .tag-charm {
            transform-origin: 108px 64px;
            animation: swingCharm 3.5s ease-in-out infinite alternate;
        }

        /* Sparkle Animation */
        .sparkle-star {
            animation: sparklePulse 3s ease-in-out infinite;
        }

        .sparkle-star-2 {
            animation: sparklePulse 3.8s ease-in-out 1.2s infinite;
        }

        /* Soft Shadow Beneath Bag */
        .bag-shadow {
            width: 110px;
            height: 14px;
            background: radial-gradient(ellipse at center, rgba(201, 88, 117, 0.18) 0%, rgba(255, 253, 252, 0) 75%);
            border-radius: 50%;
            margin-top: 0.75rem;
            animation: shadowScale 5s ease-in-out infinite;
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

        /* Footer Copy */
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

        /* Keyframe Animations */
        @keyframes floatBag {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-12px);
            }
        }

        @keyframes shadowScale {
            0%, 100% {
                transform: scale(1);
                opacity: 0.7;
            }
            50% {
                transform: scale(0.72);
                opacity: 0.35;
            }
        }

        @keyframes swingCharm {
            0% {
                transform: rotate(-6deg);
            }
            100% {
                transform: rotate(10deg);
            }
        }

        @keyframes sparklePulse {
            0%, 100% {
                opacity: 0.2;
                transform: scale(0.8);
            }
            50% {
                opacity: 1;
                transform: scale(1.2);
            }
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
            .bag-svg-container {
                width: 140px;
                height: 140px;
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
    <div class="bg-strap-curve"></div>

    <!-- Main Content -->
    <main class="hero-container">
        <span class="luxury-tag">Page Not Found</span>

        <div class="hero-composition">
            <!-- Large Elegant 404 Number -->
            <div class="number-404">404</div>

            <!-- Minimal Line-Art Handbag Animation -->
            <div class="bag-art-wrapper" id="bagWrapper" title="EEsome Luxury Handbag">
                <div class="bag-svg-container">
                    <svg class="bag-svg" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Sparkle 1 -->
                        <path class="sparkle-star" d="M32 40L34.5 47.5L42 50L34.5 52.5L32 60L29.5 52.5L22 50L29.5 47.5L32 40Z" fill="#F4B8C4" />
                        <!-- Sparkle 2 -->
                        <path class="sparkle-star-2" d="M168 135L170 141L176 143L170 145L168 151L166 145L160 143L166 141L168 135Z" fill="#E9869C" />

                        <!-- Handbag Strap (Arch Handle) -->
                        <path d="M68 92V66C68 48.3269 82.3269 34 100 34C117.673 34 132 48.3269 132 66V92" 
                              stroke="#282426" stroke-width="3" stroke-linecap="round"/>
                        <path d="M76 92V68C76 54.7452 86.7452 44 100 44C113.255 44 124 54.7452 124 68V92" 
                              stroke="#C95875" stroke-width="1.5" stroke-opacity="0.4" stroke-dasharray="3 3"/>

                        <!-- Handbag Main Body Frame -->
                        <path d="M48 92H152L144 162C143.5 166.5 139.7 170 135.1 170H64.9C60.3 170 56.5 166.5 56 162L48 92Z" 
                              fill="#FFFDFC" stroke="#282426" stroke-width="3" stroke-linejoin="round"/>
                        
                        <!-- Soft Blush Accent Inset -->
                        <path d="M54 98H146L139.5 158C139.1 161.5 136.1 164 132.5 164H67.5C63.9 164 60.9 161.5 60.5 158L54 98Z" 
                              fill="#FCECEF" fill-opacity="0.6"/>

                        <!-- Front Flap Contour -->
                        <path d="M48 92C48 92 78 126 100 126C122 126 152 92 152 92" 
                              stroke="#282426" stroke-width="2.5" stroke-linecap="round" fill="none"/>

                        <!-- Gold / Rose Clasp Detail -->
                        <rect x="92" y="120" width="16" height="12" rx="3" fill="#E9869C" stroke="#282426" stroke-width="2"/>
                        <circle cx="100" cy="126" r="2" fill="#FFFDFC"/>

                        <!-- Stitching Detail Line -->
                        <path d="M58 104H142" stroke="#E9869C" stroke-width="1.2" stroke-dasharray="4 4" stroke-opacity="0.7"/>

                        <!-- Hanging Charm/Tag (Swinging) -->
                        <g class="tag-charm">
                            <!-- Ring attachment -->
                            <circle cx="108" cy="64" r="3" fill="#C95875"/>
                            <line x1="108" y1="67" x2="114" y2="86" stroke="#282426" stroke-width="1.5"/>
                            <!-- Tag Body -->
                            <path d="M110 86L122 86L125 104L113 104Z" fill="#E9869C" stroke="#282426" stroke-width="1.5" stroke-linejoin="round"/>
                            <circle cx="116" cy="90" r="1.2" fill="#FFFDFC"/>
                        </g>
                    </svg>
                </div>
                <div class="bag-shadow"></div>
            </div>
        </div>

        <!-- Copy Text -->
        <div class="copy-section">
            <h1 class="main-headline">Looks like this bag took a detour.</h1>
            <p class="supporting-text">
                The page you’re looking for is no longer here, but your next favorite piece is only a click away.
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

    <!-- Mouse Evasion Micro-Interaction Script -->
    <script>
    (function () {
        const bag = document.getElementById('bagWrapper');
        if (!bag) return;

        bag.addEventListener('mousemove', function (e) {
            const rect = bag.getBoundingClientRect();
            const centerX = rect.left + rect.width / 2;
            const centerY = rect.top + rect.height / 2;
            const deltaX = (e.clientX - centerX) / 10;
            const deltaY = (e.clientY - centerY) / 10;

            bag.style.transform = `translate3d(${-deltaX}px, ${-deltaY - 6}px, 0) rotate(${-deltaX * 0.4}deg)`;
        });

        bag.addEventListener('mouseleave', function () {
            bag.style.transform = '';
        });
    })();
    </script>
</body>
</html>
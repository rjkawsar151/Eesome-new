<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Google Tag Manager -->
    <script>(function (w, d, s, l, i) {
            w[l] = w[l] || []; w[l].push({
                'gtm.start':
                    new Date().getTime(), event: 'gtm.js'
            }); var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : ''; j.async = true; j.src =
                    'https://www.googletagmanager.com/gtm.js?id=' + i + dl; f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-5FK7CHXW');</script>
    <!-- End Google Tag Manager -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>Page Not Found | EEsome</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --brand-50: #fdf2f8;
            --brand-100: #fce7f3;
            --brand-400: #f472b6;
            --brand-600: #db2777;
            --brand-700: #be185d;
            --brand-900: #831843;
            --ink: #17121a;
            --ink-2: #241921;
            --cream: #f9eaf3;
            --cream-muted: #d1c4cd;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: 'Outfit', system-ui, sans-serif;
            background: radial-gradient(circle at 78% 12%, rgba(219, 39, 119, .26), transparent 40%),
                radial-gradient(circle at 12% 88%, rgba(190, 24, 93, .22), transparent 38%),
                linear-gradient(150deg, #241921 0%, #17121a 55%, #120d14 100%);
            color: var(--cream);
            -webkit-font-smoothing: antialiased;
        }

        ::selection {
            background: var(--brand-100);
            color: var(--brand-900);
        }

        .err-nav {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(26, 10, 46, .88);
            -webkit-backdrop-filter: blur(22px) saturate(145%);
            backdrop-filter: blur(22px) saturate(145%);
            border-bottom: 1px solid rgba(249, 168, 212, .18);
            box-shadow: 0 8px 28px rgba(10, 4, 22, .28);
        }

        .err-nav__inner {
            width: min(calc(100% - 40px), 1200px);
            min-height: 64px;
            margin-inline: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .err-nav__logo {
            flex-shrink: 0;
            font-family: Georgia, 'Times New Roman', serif;
            font-size: clamp(1.5rem, 1.8vw, 1.65rem);
            font-weight: 700;
            letter-spacing: -.04em;
            background: linear-gradient(135deg, var(--brand-900), var(--brand-400));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-decoration: none;
        }

        .err-nav__logo img {
            display: block;
            width: clamp(150px, 14vw, 170px);
            height: 44px;
            object-fit: contain;
        }

        .err-nav__home {
            text-decoration: none;
            color: var(--cream);
            font-size: .82rem;
            font-weight: 600;
            padding: .45rem .2rem;
        }

        .err-nav__home:hover {
            color: var(--brand-400);
        }

        .err-main {
            flex: 1;
            display: grid;
            place-items: center;
            padding: 3.5rem 1.25rem 5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .err-main::before,
        .err-main::after {
            content: "";
            position: absolute;
            border-radius: 999px;
            filter: blur(9px);
            opacity: .5;
            pointer-events: none;
        }

        .err-main::before {
            width: 22rem;
            height: 22rem;
            left: -8rem;
            top: -9rem;
            background: rgba(244, 114, 182, .16);
        }

        .err-main::after {
            width: 18rem;
            height: 18rem;
            right: -6rem;
            bottom: -8rem;
            background: rgba(255, 255, 255, .05);
        }

        .err-card {
            position: relative;
            width: min(100%, 520px);
            padding: 3rem 2.5rem;
            border: 1px solid rgba(249, 168, 212, .22);
            border-radius: 1.5rem;
            background: rgba(255, 255, 255, .055);
            box-shadow: 0 28px 80px rgba(0, 0, 0, .46), inset 0 1px rgba(255, 255, 255, .1);
            -webkit-backdrop-filter: blur(24px) saturate(135%);
            backdrop-filter: blur(24px) saturate(135%);
        }

        .err-code {
            margin: 0;
            font-family: Georgia, 'Times New Roman', serif;
            font-size: clamp(5rem, 16vw, 8rem);
            font-weight: 700;
            line-height: 1;
            letter-spacing: -.04em;
            background: linear-gradient(135deg, var(--brand-400), var(--brand-900));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .err-eyebrow {
            display: inline-block;
            margin: 1.4rem 0 .4rem;
            color: var(--brand-400);
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .16em;
            text-transform: uppercase;
        }

        .err-title {
            margin: 0 0 .6rem;
            font-family: Georgia, 'Times New Roman', serif;
            font-size: clamp(1.6rem, 4vw, 2.1rem);
            font-weight: 500;
        }

        .err-text {
            margin: 0 auto 1.8rem;
            max-width: 380px;
            color: var(--cream-muted);
            line-height: 1.7;
            font-size: .95rem;
        }

        .err-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: .75rem;
        }

        .err-btn {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .8rem 1.4rem;
            border-radius: 999px;
            font: inherit;
            font-size: .88rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: transform .2s, box-shadow .2s, background-color .2s;
        }

        .err-btn-fill {
            background: var(--brand-600);
            color: #fff;
            border: none;
            box-shadow: 0 10px 26px rgba(219, 39, 119, .28);
        }

        .err-btn-fill:hover {
            background: var(--brand-700);
            transform: translateY(-2px);
        }

        .err-btn-ghost {
            border: 1.5px solid rgba(249, 168, 212, .65);
            color: var(--brand-100);
            background: rgba(255, 255, 255, .06);
        }

        .err-btn-ghost:hover {
            background: var(--brand-600);
            border-color: var(--brand-600);
            color: #fff;
            transform: translateY(-2px);
        }

        .err-footer {
            padding: 1.5rem;
            text-align: center;
            font-size: .8rem;
            color: #6b7280;
            border-top: 1px solid rgba(249, 168, 212, .12);
        }

        .err-footer a {
            color: var(--brand-400);
            font-weight: 800;
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        @media (max-width: 640px) {
            .err-main {
                padding: 2.5rem 1rem 3.5rem;
            }

            .err-card {
                padding: 2.5rem 1.5rem;
                border-radius: 1.25rem;
            }

            .err-nav__inner {
                width: min(calc(100% - 24px), 1200px);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .err-btn {
                transition: none;
            }

            .err-btn:hover {
                transform: none;
            }
        }
    </style>
</head>

<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5FK7CHXW" height="0" width="0"
            style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    @php
        $storeName = 'EEsome';
        $logoPath = null;
        try {
            $settings = app(\App\Services\SiteSettingsRepository::class);
            $storeName = $settings->get('store_name', 'EEsome');
            $logoPath = $settings->get('logo_path');
        } catch (\Throwable $e) {
        }
    @endphp

    <nav class="err-nav" aria-label="Site navigation">
        <div class="err-nav__inner">
            <a href="{{ url('/') }}" class="err-nav__logo" aria-label="{{ $storeName }} home">@if($logoPath)<img
            src="{{ asset('storage/' . $logoPath) }}" alt="{{ $storeName }}">@else{{ $storeName }}@endif</a>
            <a href="{{ url('/') }}" class="err-nav__home">Back to home</a>
        </div>
    </nav>

    <main class="err-main">
        <div class="err-card">
            <p class="err-code" aria-hidden="true">404</p>
            <span class="err-eyebrow">Oops, page not found</span>
            <h1 class="err-title">This page has wandered off</h1>
            <p class="err-text">The page you are looking for doesn't exist, was moved, or has been removed. Let's get
                you back to your perfect bag.</p>
            <div class="err-actions">
                <a href="{{ url('/') }}" class="err-btn err-btn-fill">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                        <polyline points="9 22 9 12 15 12 15 22" />
                    </svg>
                    Back to home
                </a>
                <a href="{{ url('/products') }}" class="err-btn err-btn-ghost">Browse products</a>
            </div>
        </div>
    </main>

    <footer class="err-footer">
        &copy; {{ date('Y') }} EESOME. Copyright protected. Crafted with care by <a
            href="https://www.linkedin.com/in/kawsar202/" target="_blank" rel="noopener noreferrer"
            style="text-decoration:none;color:inherit;font-weight:700;font-style:italic">KAWSAR</a>.
    </footer>
</body>

</html>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

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
                color-scheme: dark;
            }
            body { margin:0; background:#17121a; font-family:'Outfit',system-ui,sans-serif; }
            .auth-shell {
                position:relative; isolation:isolate; overflow:hidden; min-height:100vh;
                display:flex; flex-direction:column; justify-content:center; align-items:center;
                padding:2.5rem 1rem;
                background:
                    radial-gradient(circle at 78% 10%, rgba(219,39,119,.26), transparent 40%),
                    radial-gradient(circle at 12% 90%, rgba(190,24,93,.22), transparent 38%),
                    linear-gradient(150deg, #241921 0%, #17121a 55%, #120d14 100%);
            }
            .auth-shell::before,.auth-shell::after { content:""; position:absolute; z-index:-1; border-radius:999px; filter:blur(9px); opacity:.5; }
            .auth-shell::before { width:22rem; height:22rem; left:-8rem; top:-9rem; background:rgba(244,114,182,.16); }
            .auth-shell::after { width:18rem; height:18rem; right:-6rem; bottom:-8rem; background:rgba(255,255,255,.05); }
            .auth-logo { padding:.7rem 1.25rem; border:1px solid rgba(249,168,212,.22); border-radius:1rem; background:rgba(255,255,255,.06); box-shadow:0 12px 40px rgba(0,0,0,.22); backdrop-filter:blur(18px); -webkit-backdrop-filter:blur(18px); }
            .auth-logo a { text-decoration:none; }
            .auth-card {
                color:#f6f2ee;
                border:1px solid rgba(249,168,212,.22);
                background:linear-gradient(145deg, rgba(255,255,255,.12), rgba(255,255,255,.055));
                box-shadow:0 28px 80px rgba(0,0,0,.46), inset 0 1px rgba(255,255,255,.12);
                backdrop-filter:blur(24px) saturate(135%);
                -webkit-backdrop-filter:blur(24px) saturate(135%);
            }
            .auth-card label, .auth-card .text-gray-600, .auth-card .text-gray-700 { color:#ded6cf!important; }
            .auth-card input[type="text"], .auth-card input[type="email"], .auth-card input[type="password"] {
                color:#fff;
                background:rgba(8,8,8,.38);
                border-color:rgba(255,255,255,.18);
                box-shadow:inset 0 1px 0 rgba(255,255,255,.04);
            }
            .auth-card input::placeholder { color:#8f8781; }
            .auth-card input:focus { border-color:#f472b6!important; --tw-ring-color:rgba(244,114,182,.4)!important; }
            .auth-card button[type="submit"], .auth-card .btn-brand {
                background:linear-gradient(135deg,#db2777,#be185d);
                box-shadow:0 10px 24px rgba(219,39,119,.28);
                border-radius:999px;
            }
            .auth-card button[type="submit"]:hover, .auth-card .btn-brand:hover { background:linear-gradient(135deg,#e0468b,#c9246c); }
            .auth-card a { color:#f9a8d4; }
            .auth-card .or-divider { border-color:rgba(255,255,255,.14); color:#8f8781; }
            .auth-card .or-divider span { background:#211a20; }
            @media(max-width:640px){ .auth-shell{padding:1.25rem} .auth-card{border-radius:1.25rem;padding:1.35rem} .auth-logo img{max-width:170px!important;max-height:72px!important} }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="auth-shell">
            @php
                $settings = app(\App\Services\SiteSettingsRepository::class);
                $authLogoPath = $settings->get('logo_path');
                $authStoreName = $settings->get('store_name', config('app.name', 'EESOME'));
            @endphp
            <div class="auth-logo">
                <a href="{{ route('home') }}" aria-label="{{ $authStoreName }} home" class="inline-flex items-center justify-center">
                    @if($authLogoPath)
                        <img src="{{ asset('storage/'.$authLogoPath) }}" alt="{{ $authStoreName }}" class="w-auto object-contain" style="max-width:220px;max-height:96px">
                    @else
                        <span style="font-family:Georgia,'Times New Roman',serif;font-size:1.7rem;font-weight:700;letter-spacing:-.04em;background:linear-gradient(135deg,#831843,#f472b6);-webkit-background-clip:text;-webkit-text-fill-color:transparent">{{ $authStoreName }}</span>
                    @endif
                </a>
            </div>

            <div class="auth-card w-full sm:max-w-md mt-6 px-7 py-7 overflow-hidden rounded-2xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>

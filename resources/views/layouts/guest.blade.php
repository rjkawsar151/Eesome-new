<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <style>
            :root { color-scheme: dark; }
            body { background:#11100f; }
            .auth-shell { position:relative; isolation:isolate; overflow:hidden; background:radial-gradient(circle at 15% 10%,rgba(168,122,85,.28),transparent 34%),radial-gradient(circle at 85% 85%,rgba(91,65,48,.28),transparent 38%),linear-gradient(145deg,#24201d 0%,#151311 48%,#090909 100%); }
            .auth-shell::before,.auth-shell::after { content:"";position:absolute;z-index:-1;border-radius:999px;filter:blur(8px);opacity:.65; }
            .auth-shell::before { width:22rem;height:22rem;left:-8rem;bottom:-10rem;background:rgba(190,139,94,.17); }
            .auth-shell::after { width:18rem;height:18rem;right:-6rem;top:-7rem;background:rgba(255,255,255,.07); }
            .auth-logo { padding:.7rem 1.25rem;border:1px solid rgba(255,255,255,.12);border-radius:1rem;background:rgba(255,255,255,.06);box-shadow:0 12px 40px rgba(0,0,0,.22);backdrop-filter:blur(18px);-webkit-backdrop-filter:blur(18px); }
            .auth-card { color:#f6f2ee;border:1px solid rgba(255,255,255,.16);background:linear-gradient(145deg,rgba(255,255,255,.12),rgba(255,255,255,.055));box-shadow:0 28px 80px rgba(0,0,0,.46),inset 0 1px rgba(255,255,255,.12);backdrop-filter:blur(24px) saturate(135%);-webkit-backdrop-filter:blur(24px) saturate(135%); }
            .auth-card label,.auth-card .text-gray-600,.auth-card .text-gray-700 { color:#ded6cf!important; }
            .auth-card input[type="text"],.auth-card input[type="email"],.auth-card input[type="password"] { color:#fff;background:rgba(8,8,8,.38);border-color:rgba(255,255,255,.18);box-shadow:inset 0 1px 0 rgba(255,255,255,.04); }
            .auth-card input::placeholder { color:#8f8781; }
            .auth-card input:focus { border-color:#c59670!important;--tw-ring-color:rgba(197,150,112,.38)!important; }
            .auth-card button[type="submit"] { background:linear-gradient(135deg,#b47d54,#8a5736);box-shadow:0 10px 24px rgba(100,55,28,.34); }
            .auth-card button[type="submit"]:hover { background:linear-gradient(135deg,#c38d64,#98613e); }
            .auth-card a { color:#ead9ca; }
            @media(max-width:640px){.auth-shell{padding:1.25rem}.auth-card{border-radius:1.25rem;padding:1.35rem}.auth-logo img{max-width:170px!important;max-height:72px!important}}
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="auth-shell min-h-screen flex flex-col justify-center items-center py-8 px-4">
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
                        <span class="text-3xl font-bold tracking-tight text-white">{{ $authStoreName }}</span>
                    @endif
                </a>
            </div>

            <div class="auth-card w-full sm:max-w-md mt-6 px-7 py-7 overflow-hidden rounded-2xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>

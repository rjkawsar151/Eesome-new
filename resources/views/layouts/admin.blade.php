<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-5FK7CHXW');</script>
    <!-- End Google Tag Manager -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | EEsome Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root{--ink:#172033;--muted:#64748b;--brand:#be185d;--soft:#fdf2f8;--line:#e8eaf0}
        *{box-sizing:border-box}body{margin:0;background:#f6f7fb;color:var(--ink);font-family:Inter,ui-sans-serif,system-ui,sans-serif}
        .admin-shell{min-height:100vh;display:grid;grid-template-columns:minmax(0,240px) minmax(0,1fr)}.sidebar{min-width:0;background:#172033;color:#fff;padding:1.5rem;position:sticky;top:0;height:100vh;overflow-x:hidden;overflow-y:auto}
        .brand{font-size:1.5rem;font-weight:800;color:#fff;text-decoration:none;display:block}.brand span{color:#f9a8d4}.side-nav{display:grid;min-width:0;gap:.4rem;margin-top:2rem}.side-nav a{display:flex;align-items:center;gap:.6rem;width:100%;max-width:100%;min-width:0;color:#cbd5e1;text-decoration:none;padding:.75rem .9rem;border-radius:.7rem;overflow-wrap:anywhere}.side-nav a svg{width:18px;height:18px;flex-shrink:0}.side-nav a span{min-width:0}.side-nav a:hover,.side-nav a.active{background:#be185d;color:#fff}
        .side-foot{margin-top:1.5rem;padding-top:1rem;border-top:1px solid rgba(255,255,255,.12)}.side-foot form{margin:0}.side-foot button{display:flex;width:100%;align-items:center;gap:.6rem;border:0;background:transparent;color:#cbd5e1;padding:.75rem .9rem;border-radius:.7rem;font:inherit;font-size:1rem;cursor:pointer;text-align:left}.side-foot button:hover,.side-foot button:focus-visible{background:#be185d;color:#fff}.side-foot button svg{width:18px;height:18px;flex-shrink:0}
        .admin-main{min-width:0}.topbar{height:68px;background:#fff;border-bottom:1px solid var(--line);display:flex;align-items:center;justify-content:space-between;padding:0 2rem;gap:1rem}.topbar-left{display:flex;align-items:center;gap:.75rem;min-width:0}.topbar-left strong{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.page{padding:2rem;max-width:1500px}
        .title{margin:0;font-size:1.65rem}.subtle{color:var(--muted)}.card{background:#fff;border:1px solid var(--line);border-radius:1rem;padding:1.25rem;box-shadow:0 2px 12px rgba(15,23,42,.04)}
        .grid{display:grid;gap:1rem}.stats{grid-template-columns:repeat(auto-fit,minmax(180px,1fr))}.stat-value{font-size:1.8rem;font-weight:800;margin-top:.35rem}.two-col{grid-template-columns:minmax(0,2fr) minmax(280px,1fr)}
        .table-wrap{overflow:auto}.table{width:100%;border-collapse:collapse}.table th,.table td{padding:.8rem;text-align:left;border-bottom:1px solid var(--line);white-space:nowrap}.table th{font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;color:var(--muted)}
        .btn{display:inline-flex;align-items:center;justify-content:center;border:0;border-radius:.65rem;padding:.65rem 1rem;font-weight:700;text-decoration:none;cursor:pointer}.btn-primary{background:var(--brand);color:#fff}.btn-soft{background:var(--soft);color:var(--brand)}.btn-danger{background:#fee2e2;color:#b91c1c}
        .field{display:grid;gap:.35rem}.field label{font-weight:700;font-size:.85rem}.input,.select,.textarea{width:100%;border:1px solid #cbd5e1;border-radius:.65rem;padding:.7rem .8rem;background:#fff}.textarea{min-height:110px}.form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1rem}.full{grid-column:1/-1}
        .badge{display:inline-flex;padding:.25rem .55rem;border-radius:999px;font-size:.75rem;font-weight:700;background:#eef2ff;color:#4338ca}.badge-green{background:#dcfce7;color:#166534}.badge-yellow{background:#fef3c7;color:#92400e}.badge-red{background:#fee2e2;color:#991b1b}
        .toolbar{display:flex;gap:.75rem;align-items:end;flex-wrap:wrap;margin-bottom:1rem}.toolbar .field{min-width:180px}.pagination{margin-top:1rem}.alert{padding:.8rem 1rem;border-radius:.7rem;margin-bottom:1rem}.success{background:#dcfce7;color:#166534}.error{background:#fee2e2;color:#991b1b}
        .nav-toggle{display:none;align-items:center;justify-content:center;width:42px;height:42px;border:0;border-radius:.65rem;background:var(--soft);color:var(--brand);cursor:pointer;flex-shrink:0}.nav-toggle svg{display:block;width:24px;height:24px}
        .drawer-backdrop{position:fixed;inset:0;background:rgba(15,23,42,.5);z-index:65;opacity:0;pointer-events:none;transition:opacity .3s ease}
        @media(max-width:900px){.admin-shell{grid-template-columns:1fr}.nav-toggle{display:inline-flex}.sidebar{position:fixed;top:0;left:0;width:280px;max-width:85vw;height:100vh;padding:1.25rem;transform:translateX(-100%);transition:transform .3s ease;z-index:70;box-shadow:0 0 40px rgba(15,23,42,.35)}.sidebar.open{transform:translateX(0)}.drawer-backdrop.show{opacity:1;pointer-events:auto}.drawer-open{overflow:hidden}.two-col{grid-template-columns:1fr}.page{padding:1rem}.topbar{padding:0 .75rem}}
        @media(max-width:600px){.form-grid{grid-template-columns:1fr}.full{grid-column:auto}}
    </style>
    @stack('styles')
</head>
<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5FK7CHXW"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<div class="admin-shell">
    <aside class="sidebar">
        <a class="brand" href="{{ route('admin.dashboard') }}">EE<span>some</span></a>
        <nav class="side-nav" id="admin-nav" aria-label="Admin navigation">
            @php
                $role = auth()->user()->role;
                $fullAdmin = in_array($role, ['admin', 'super admin'], true);
                $operations = in_array($role, ['admin', 'super admin', 'manager'], true);
                $content = in_array($role, ['admin', 'super admin', 'content editor'], true);
            @endphp
            <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg><span>Dashboard</span></a>
            @if($operations)<a class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg><span>Orders</span></a>@endif
            <a class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg><span>Products</span></a>
            <a class="{{ request()->routeIs('admin.hero-products.*') ? 'active' : '' }}" href="{{ route('admin.hero-products.edit') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/></svg><span>Hero Products</span></a>
            @if($operations)<a class="{{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}" href="{{ route('admin.inventory.index') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M12 11h4"/><path d="M12 16h4"/><path d="M8 11h.01"/><path d="M8 16h.01"/></svg><span>Inventory</span></a>@endif
            <a class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2Z"/></svg><span>Categories</span></a>
            <a class="{{ request()->routeIs('admin.brands.*') ? 'active' : '' }}" href="{{ route('admin.brands.index') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg><span>Brands</span></a>
            <a class="{{ request()->routeIs('admin.tags.*') ? 'active' : '' }}" href="{{ route('admin.tags.index') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12.586 2.586A2 2 0 0 0 11.172 2H4a2 2 0 0 0-2 2v7.172a2 2 0 0 0 .586 1.414l8.704 8.704a2.426 2.426 0 0 0 3.42 0l6.58-6.58a2.426 2.426 0 0 0 0-3.42z"/><circle cx="7.5" cy="7.5" r=".5" fill="currentColor"/></svg><span>Tags</span></a>
            <a class="{{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}" href="{{ route('admin.reviews.index') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><path d="M8 9h8"/><path d="M8 13h5"/></svg><span>Reviews</span></a>
            @if($content)<a class="{{ request()->routeIs('admin.blog.*') ? 'active' : '' }}" href="{{ route('admin.blog.index') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/></svg><span>Blog</span></a>@endif
            @if($fullAdmin)
                <a class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg><span>Users &amp; Roles</span></a>
                <a class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.edit') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg><span>Settings</span></a>
                <a class="{{ request()->routeIs('admin.shipping-methods.*') ? 'active' : '' }}" href="{{ route('admin.shipping-methods.index') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg><span>Shipping methods</span></a>
                <a class="{{ request()->routeIs('admin.payment-methods.*') ? 'active' : '' }}" href="{{ route('admin.payment-methods.index') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg><span>Payment methods</span></a>
                <a class="{{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}" href="{{ route('admin.coupons.index') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/><path d="M9 9h.01"/><path d="m15 9-6 6"/><path d="M15 15h.01"/></svg><span>Coupons</span></a>
                <a class="{{ request()->routeIs('admin.navigation-items.*') ? 'active' : '' }}" href="{{ route('admin.navigation-items.index') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 11 22 2l-9 19-2-8-8-2Z"/></svg><span>Navigation</span></a>
                <a class="{{ request()->routeIs('admin.media.*') ? 'active' : '' }}" href="{{ route('admin.media.index') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg><span>Media library</span></a>
                <a class="{{ request()->routeIs('admin.activity.*') ? 'active' : '' }}" href="{{ route('admin.activity.index') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 12h-2.48a2 2 0 0 0-1.93 1.46l-2.35 8.36a.25.25 0 0 1-.48 0L9.24 2.18a.25.25 0 0 0-.48 0l-2.35 8.36A2 2 0 0 1 4.49 12H2"/></svg><span>Activity logs</span></a>
                <a class="{{ request()->routeIs('admin.visitors.*') ? 'active' : '' }}" href="{{ route('admin.visitors.index') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="12" x2="12" y1="20" y2="10"/><line x1="18" x2="18" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="16"/></svg><span>Visitor stats</span></a>
            @endif
            <a href="{{ route('home') }}" target="_blank" rel="noopener"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg><span>View storefront</span></a>
        </nav>
        <div class="side-foot"><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>Log out</button></form></div>
    </aside>
    <div class="drawer-backdrop" id="drawer-backdrop" hidden></div>
    <main class="admin-main">
        <header class="topbar"><div class="topbar-left"><button class="nav-toggle" id="nav-toggle" type="button" aria-label="Open navigation" aria-expanded="false" aria-controls="admin-nav"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/></svg></button><strong>@yield('heading', 'Admin')</strong></div><span class="subtle">{{ auth()->user()->name }}</span></header>
        <div class="page">
            @if(session('success'))<div class="alert success">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="alert error">{{ session('error') }}</div>@endif
            @if($errors->any())<div class="alert error"><ul style="margin:0;padding-left:1.2rem">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            @yield('content')
        </div>
    </main>
</div>
@stack('scripts')
<script>
(function () {
    if ('scrollRestoration' in history) history.scrollRestoration = 'manual';
    var KEY = 'admin_scroll_pos';
    window.addEventListener('beforeunload', function () {
        sessionStorage.setItem(KEY, String(window.scrollY || 0));
    });
    var saved = sessionStorage.getItem(KEY);
    if (saved !== null && saved !== '') {
        window.scrollTo(0, parseInt(saved, 10) || 0);
    }
})();
(function () {
    var toggle = document.getElementById('nav-toggle');
    var drawer = document.querySelector('.sidebar');
    var backdrop = document.getElementById('drawer-backdrop');
    if (!toggle || !drawer) return;
    var isOpen = function () { return drawer.classList.contains('open'); };
    function open() {
        drawer.classList.add('open');
        backdrop.hidden = false;
        requestAnimationFrame(function () { backdrop.classList.add('show'); });
        document.body.classList.add('drawer-open');
        toggle.setAttribute('aria-expanded', 'true');
        toggle.setAttribute('aria-label', 'Close navigation');
    }
    function close() {
        drawer.classList.remove('open');
        backdrop.classList.remove('show');
        document.body.classList.remove('drawer-open');
        toggle.setAttribute('aria-expanded', 'false');
        toggle.setAttribute('aria-label', 'Open navigation');
    }
    toggle.addEventListener('click', function () { isOpen() ? close() : open(); });
    backdrop.addEventListener('click', close);
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });
    drawer.querySelectorAll('a').forEach(function (link) { link.addEventListener('click', close); });
    backdrop.addEventListener('transitionend', function () { if (!backdrop.classList.contains('show')) backdrop.hidden = true; });
    window.addEventListener('resize', function () { if (window.innerWidth > 900) close(); });
})();
</script>
</body>
</html>

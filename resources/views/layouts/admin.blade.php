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
        .brand{font-size:1.5rem;font-weight:800;color:#fff;text-decoration:none}.brand span{color:#f9a8d4}.side-nav{display:grid;min-width:0;gap:.4rem;margin-top:2rem}.side-nav a{display:block;width:100%;max-width:100%;min-width:0;color:#cbd5e1;text-decoration:none;padding:.75rem .9rem;border-radius:.7rem;overflow-wrap:anywhere}.side-nav a:hover,.side-nav a.active{background:#be185d;color:#fff}
        .admin-main{min-width:0}.topbar{height:68px;background:#fff;border-bottom:1px solid var(--line);display:flex;align-items:center;justify-content:space-between;padding:0 2rem}.page{padding:2rem;max-width:1500px}
        .title{margin:0;font-size:1.65rem}.subtle{color:var(--muted)}.card{background:#fff;border:1px solid var(--line);border-radius:1rem;padding:1.25rem;box-shadow:0 2px 12px rgba(15,23,42,.04)}
        .grid{display:grid;gap:1rem}.stats{grid-template-columns:repeat(auto-fit,minmax(180px,1fr))}.stat-value{font-size:1.8rem;font-weight:800;margin-top:.35rem}.two-col{grid-template-columns:minmax(0,2fr) minmax(280px,1fr)}
        .table-wrap{overflow:auto}.table{width:100%;border-collapse:collapse}.table th,.table td{padding:.8rem;text-align:left;border-bottom:1px solid var(--line);white-space:nowrap}.table th{font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;color:var(--muted)}
        .btn{display:inline-flex;align-items:center;justify-content:center;border:0;border-radius:.65rem;padding:.65rem 1rem;font-weight:700;text-decoration:none;cursor:pointer}.btn-primary{background:var(--brand);color:#fff}.btn-soft{background:var(--soft);color:var(--brand)}.btn-danger{background:#fee2e2;color:#b91c1c}
        .field{display:grid;gap:.35rem}.field label{font-weight:700;font-size:.85rem}.input,.select,.textarea{width:100%;border:1px solid #cbd5e1;border-radius:.65rem;padding:.7rem .8rem;background:#fff}.textarea{min-height:110px}.form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1rem}.full{grid-column:1/-1}
        .badge{display:inline-flex;padding:.25rem .55rem;border-radius:999px;font-size:.75rem;font-weight:700;background:#eef2ff;color:#4338ca}.badge-green{background:#dcfce7;color:#166534}.badge-yellow{background:#fef3c7;color:#92400e}.badge-red{background:#fee2e2;color:#991b1b}
        .toolbar{display:flex;gap:.75rem;align-items:end;flex-wrap:wrap;margin-bottom:1rem}.toolbar .field{min-width:180px}.pagination{margin-top:1rem}.alert{padding:.8rem 1rem;border-radius:.7rem;margin-bottom:1rem}.success{background:#dcfce7;color:#166534}.error{background:#fee2e2;color:#991b1b}
        @media(max-width:900px){.admin-shell{grid-template-columns:1fr}.sidebar{height:auto;position:static}.side-nav{display:flex;overflow:auto;margin-top:1rem}.side-nav a{width:auto;max-width:none;flex:0 0 auto;overflow-wrap:normal}.two-col{grid-template-columns:1fr}.page{padding:1rem}.topbar{padding:0 1rem}}
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
        <nav class="side-nav" aria-label="Admin navigation">
            @php
                $role = auth()->user()->role;
                $fullAdmin = in_array($role, ['admin', 'super admin'], true);
                $operations = in_array($role, ['admin', 'super admin', 'manager'], true);
                $content = in_array($role, ['admin', 'super admin', 'content editor'], true);
            @endphp
            <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
            @if($operations)<a class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">Orders</a>@endif
            <a class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">Products</a>
            <a class="{{ request()->routeIs('admin.hero-products.*') ? 'active' : '' }}" href="{{ route('admin.hero-products.edit') }}">Hero Products</a>
            @if($operations)<a class="{{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}" href="{{ route('admin.inventory.index') }}">Inventory</a>@endif
            <a class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">Categories</a>
            <a class="{{ request()->routeIs('admin.brands.*') ? 'active' : '' }}" href="{{ route('admin.brands.index') }}">Brands</a>
            <a class="{{ request()->routeIs('admin.tags.*') ? 'active' : '' }}" href="{{ route('admin.tags.index') }}">Tags</a>
            <a class="{{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}" href="{{ route('admin.reviews.index') }}">Reviews</a>
            @if($content)<a class="{{ request()->routeIs('admin.blog.*') ? 'active' : '' }}" href="{{ route('admin.blog.index') }}">Blog</a>@endif
            @if($fullAdmin)
                <a class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">Users & Roles</a>
                <a class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.edit') }}">Settings</a>
                <a class="{{ request()->routeIs('admin.shipping-methods.*') ? 'active' : '' }}" href="{{ route('admin.shipping-methods.index') }}">Shipping methods</a>
                <a class="{{ request()->routeIs('admin.payment-methods.*') ? 'active' : '' }}" href="{{ route('admin.payment-methods.index') }}">Payment methods</a>
                <a class="{{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}" href="{{ route('admin.coupons.index') }}">Coupons</a>
                <a class="{{ request()->routeIs('admin.navigation-items.*') ? 'active' : '' }}" href="{{ route('admin.navigation-items.index') }}">Navigation</a>
                <a class="{{ request()->routeIs('admin.media.*') ? 'active' : '' }}" href="{{ route('admin.media.index') }}">Media library</a>
                <a class="{{ request()->routeIs('admin.activity.*') ? 'active' : '' }}" href="{{ route('admin.activity.index') }}">Activity logs</a>
                <a class="{{ request()->routeIs('admin.visitors.*') ? 'active' : '' }}" href="{{ route('admin.visitors.index') }}">Visitor stats</a>
            @endif
            <a href="{{ route('home') }}" target="_blank" rel="noopener">View storefront &rarr;</a>
        </nav>
    </aside>
    <main class="admin-main">
        <header class="topbar"><strong>@yield('heading', 'Admin')</strong><span class="subtle">{{ auth()->user()->name }}</span></header>
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
</script>
</body>
</html>

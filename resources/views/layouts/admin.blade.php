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
        *{box-sizing:border-box}
        html,body{max-width:100%;overflow-x:clip}
        body{margin:0;background:#f6f7fb;color:var(--ink);font-family:Inter,ui-sans-serif,system-ui,sans-serif;font-size:15px;line-height:1.5;-webkit-text-size-adjust:100%}
        .admin-shell{min-height:100vh;display:grid;grid-template-columns:minmax(0,240px) minmax(0,1fr)}
        .sidebar{min-width:0;background:#172033;color:#fff;padding:1.15rem .85rem;position:sticky;top:0;height:100vh;overflow-x:hidden;overflow-y:auto}
        .brand{font-size:1.3rem;font-weight:800;color:#fff;text-decoration:none;display:block;padding:.1rem .35rem}.brand span{color:#f9a8d4}
        .side-nav{display:grid;min-width:0;gap:.28rem;margin-top:1.1rem}
        .side-nav a{display:flex;align-items:center;gap:.55rem;width:100%;max-width:100%;min-width:0;color:#cbd5e1;text-decoration:none;padding:.58rem .7rem;border-radius:.55rem;font-size:.9rem;overflow-wrap:anywhere}
        .side-nav a svg{width:17px;height:17px;flex-shrink:0}.side-nav a span{min-width:0;overflow:hidden;text-overflow:ellipsis}
        .side-nav a:hover,.side-nav a.active{background:#be185d;color:#fff}
        .side-foot{margin-top:1rem;padding-top:.75rem;border-top:1px solid rgba(255,255,255,.12)}.side-foot form{margin:0}
        .side-foot button{display:flex;width:100%;align-items:center;gap:.55rem;border:0;background:transparent;color:#cbd5e1;padding:.58rem .7rem;border-radius:.55rem;font:inherit;font-size:.9rem;cursor:pointer;text-align:left}
        .side-foot button:hover,.side-foot button:focus-visible{background:#be185d;color:#fff}.side-foot button svg{width:17px;height:17px;flex-shrink:0}
        .admin-main{min-width:0}
        .topbar{height:60px;background:#fff;border-bottom:1px solid var(--line);display:flex;align-items:center;justify-content:space-between;padding:0 1.5rem;gap:.75rem;min-width:0}
        .topbar-left{display:flex;align-items:center;gap:.65rem;min-width:0;flex:1 1 auto}
        .topbar-left strong{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:1rem}
        .topbar>.subtle{min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:40%;flex-shrink:1;font-size:.85rem}
        .page{padding:1.5rem;max-width:1500px;min-width:0}
        .title{margin:0;font-size:1.5rem;font-weight:800;line-height:1.25;letter-spacing:-.01em}
        .subtle{color:var(--muted)}
        .card{background:#fff;border:1px solid var(--line);border-radius:.85rem;padding:1.1rem;box-shadow:0 2px 12px rgba(15,23,42,.04);min-width:0}
        .grid{display:grid;gap:1rem}.stats{grid-template-columns:repeat(auto-fit,minmax(170px,1fr))}
        .stat-value{font-size:1.6rem;font-weight:800;margin-top:.3rem;line-height:1.2}
        .two-col{grid-template-columns:minmax(0,2fr) minmax(280px,1fr)}
        .table-wrap{overflow:auto;-webkit-overflow-scrolling:touch;max-width:100%}
        .table{width:100%;border-collapse:collapse;min-width:0}
        .table th,.table td{padding:.68rem .75rem;text-align:left;border-bottom:1px solid var(--line);white-space:nowrap}
        .table th{font-size:.72rem;text-transform:uppercase;letter-spacing:.05em;color:var(--muted)}
        .btn{display:inline-flex;align-items:center;justify-content:center;border:0;border-radius:.55rem;padding:.55rem .85rem;font-weight:700;font-size:.9rem;line-height:1.2;text-decoration:none;cursor:pointer;transition:filter .15s ease,transform .05s ease}
        .btn:active{transform:scale(.98)}
        .btn-primary{background:var(--brand);color:#fff}.btn-soft{background:var(--soft);color:var(--brand)}.btn-danger{background:#fee2e2;color:#b91c1c}
        .btn-sm{padding:.38rem .6rem;font-size:.82rem}
        .field{display:grid;gap:.28rem;min-width:0}.field label{font-weight:700;font-size:.82rem}
        .input,.select,.textarea{width:100%;min-width:0;border:1px solid #cbd5e1;border-radius:.5rem;padding:.6rem .75rem;background:#fff;color:var(--ink);font-size:16px;font-family:inherit;line-height:1.4}
        .input:focus,.select:focus,.textarea:focus{outline:2px solid var(--brand);outline-offset:-1px;border-color:var(--brand)}
        .textarea{min-height:110px}
        .form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1rem}
        .full{grid-column:1/-1;min-width:0}
        .badge{display:inline-flex;align-items:center;padding:.2rem .5rem;border-radius:999px;font-size:.72rem;font-weight:700;background:#eef2ff;color:#4338ca;white-space:nowrap}
        .badge-green{background:#dcfce7;color:#166534}.badge-yellow{background:#fef3c7;color:#92400e}.badge-red{background:#fee2e2;color:#991b1b}
        .toolbar{display:flex;gap:.75rem;align-items:end;flex-wrap:wrap;margin-bottom:1rem}
        .toolbar .field{min-width:180px;flex:1 1 auto}
        .pagination{margin-top:1rem}
        .alert{padding:.7rem .9rem;border-radius:.6rem;margin-bottom:1rem;font-size:.9rem}
        .success{background:#dcfce7;color:#166534}.error{background:#fee2e2;color:#991b1b}
        .nav-toggle{display:none;align-items:center;justify-content:center;width:40px;height:40px;border:0;border-radius:.6rem;background:var(--soft);color:var(--brand);cursor:pointer;flex-shrink:0}
        .nav-toggle svg{display:block;width:22px;height:22px}
        .drawer-backdrop{position:fixed;inset:0;background:rgba(15,23,42,.5);z-index:65;opacity:0;pointer-events:none;transition:opacity .3s ease}
        .page-head{display:flex;justify-content:space-between;align-items:center;gap:.75rem;flex-wrap:wrap}
        .page-head .actions{display:flex;align-items:center;gap:.5rem;flex-wrap:wrap}
        .section-head{display:flex;justify-content:space-between;align-items:center;gap:.75rem;flex-wrap:wrap}
        .form-section+.form-section{border-top:1px solid var(--line);padding-top:1.05rem;margin-top:1.05rem}
        .form-section-title{margin:0 0 .75rem;font-size:.95rem;font-weight:800;text-transform:uppercase;letter-spacing:.04em;color:var(--muted)}
        .form-actions{display:flex;gap:.5rem;flex-wrap:wrap;margin-top:.6rem}
        .form-actions .btn{flex:1 1 auto}
        .req{color:#e11d48;margin-left:.2rem}
        .check-label{display:inline-flex;align-items:center;gap:.45rem;font-weight:600;font-size:.9rem;cursor:pointer;line-height:1.4}
        .check-label input{width:1.05rem;height:1.05rem;flex-shrink:0;accent-color:var(--brand)}
        .check-row{display:flex;gap:1.1rem;flex-wrap:wrap;align-items:center}
        .image-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(96px,1fr));gap:.75rem}
        .image-grid .img-item{display:grid;gap:.35rem;justify-items:start}
        .image-grid img{width:96px;height:96px;object-fit:contain;border:1px solid var(--line);border-radius:.55rem;background:#fff}
        .variant-fields{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.85rem}
        .variant-toggles{display:flex;gap:1rem;flex-wrap:wrap;align-items:center;padding-top:.2rem}
        .swatch-wrap{display:flex;align-items:center;gap:.5rem}
        .input.swatch{flex:0 0 auto;width:56px;height:42px;padding:.15rem;cursor:pointer}
        .inline-form{display:flex;gap:.4rem;flex-wrap:wrap;align-items:center}
        .inline-form .input{flex:1 1 100px;min-width:0;width:auto}
        .rich-toolbar{display:flex;flex-wrap:wrap;gap:.35rem;padding:.45rem;background:#f8fafc;border:1px solid #cbd5e1;border-radius:.5rem .5rem 0 0}
        .rich-toolbar button{padding:.3rem .6rem;font-size:.85rem;border:1px solid #e2e8f0;background:#fff;border-radius:.4rem;cursor:pointer}
        .rich-editor{min-height:220px;border-radius:0 0 .5rem .5rem}
        @media(max-width:900px){
            .admin-shell{grid-template-columns:1fr}.nav-toggle{display:inline-flex}
            .sidebar{position:fixed;top:0;left:0;width:280px;max-width:85vw;height:100vh;padding:.9rem;transform:translateX(-100%);transition:transform .3s ease;z-index:70;box-shadow:0 0 40px rgba(15,23,42,.35)}
            .sidebar.open{transform:translateX(0)}.drawer-backdrop.show{opacity:1;pointer-events:auto}.drawer-open{overflow:hidden}
            .two-col{grid-template-columns:1fr}.page{padding:1rem}.topbar{padding:0 .75rem}
        }
        @media(max-width:720px){
            .form-grid,.variant-fields{grid-template-columns:1fr}.full{grid-column:auto}
            .title{font-size:1.3rem}.card{padding:.9rem;border-radius:.75rem}
            .stat-value{font-size:1.35rem}.grid{gap:.7rem}
            .toolbar{gap:.6rem}.toolbar .field{min-width:100%;flex:1 1 100%}
            .table th,.table td{padding:.55rem .6rem}
            .topbar{height:56px}.topbar>.subtle{max-width:45%}
            .page{padding:.85rem}
            .form-actions .btn{flex:1 1 100%}
        }
        @media(max-width:640px){
            .table-wrap{overflow:visible}
            .table,.table tbody,.table tr,.table td{display:block;width:100%}
            .table thead{display:none}
            .table tr{border:1px solid var(--line);border-radius:.7rem;padding:.5rem .75rem;margin-bottom:.7rem;background:#fff}
            .table td{display:block;width:100%;padding:.42rem 0;border-bottom:1px dashed var(--line);white-space:normal;overflow-wrap:break-word;word-break:break-word}
            .table td:last-child{border-bottom:0;display:flex;flex-wrap:wrap;gap:.4rem;align-items:center}
            .table td::before{content:attr(data-label);display:block;font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.05em;color:var(--muted);margin-bottom:.12rem;width:100%}
            .table td:not([data-label])::before,.table td[data-label=""]::before,.table td[colspan]::before{display:none}
            .table td[colspan]{text-align:center;color:var(--muted);padding:.4rem 0;border:0}
            .table tr:has(td[colspan]){border:0;background:transparent;padding:0}
            .table td:last-child .btn{flex:1 1 auto}
            .table .inline-form .input{flex:1 1 100%}
        }
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
(function () {
    function labelTables() {
        document.querySelectorAll('.table-wrap .table').forEach(function (table) {
            var headers = [];
            table.querySelectorAll('thead th').forEach(function (th, i) {
                headers.push((th.textContent || '').trim());
            });
            table.querySelectorAll('tbody tr').forEach(function (row) {
                var cells = row.querySelectorAll('td');
                for (var i = 0; i < cells.length; i++) {
                    var td = cells[i];
                    if (td.hasAttribute('colspan')) continue;
                    var label = headers[i] || '';
                    if (label) td.setAttribute('data-label', label);
                }
            });
        });
    }
    var mq = window.matchMedia && window.matchMedia('(max-width: 640px)');
    if (mq && mq.addEventListener) mq.addEventListener('change', labelTables);
    labelTables();
})();
</script>
</body>
</html>

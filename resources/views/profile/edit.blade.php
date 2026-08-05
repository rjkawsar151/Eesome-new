<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">My account</h2></x-slot>
    <style>
        .acc{max-width:1200px;margin-inline:auto;padding:2rem 1rem 4rem}

        .acc-hero{display:flex;align-items:center;gap:1rem;flex-wrap:wrap;background:linear-gradient(135deg,#1c1420,#2a1f2e);border:1px solid rgba(249,168,212,.18);border-radius:1.25rem;padding:1.25rem 1.5rem;color:#fff;box-shadow:0 10px 30px rgba(45,27,38,.12)}
        .acc-hero__avatar{width:64px;height:64px;border-radius:50%;flex-shrink:0;overflow:hidden;border:2px solid rgba(249,168,212,.55);background:linear-gradient(135deg,#db2777,#831843);display:grid;place-items:center;font-weight:800;font-size:1.4rem;color:#fff}
        .acc-hero__avatar img{width:100%;height:100%;object-fit:cover;display:block}
        .acc-hero__meta{min-width:0;flex:1 1 200px}
        .acc-hero__meta h1{margin:0;font-size:1.3rem;color:#fff}
        .acc-hero__meta p{margin:.2rem 0 0;color:#d1c4cd;font-size:.85rem;word-break:break-all}
        .acc-hero__actions{display:flex;gap:.6rem;flex-wrap:wrap}
        .acc-hero__btn{display:inline-flex;align-items:center;gap:.45rem;min-height:42px;padding:.55rem 1.05rem;border-radius:999px;font-size:.82rem;font-weight:700;text-decoration:none;transition:background .15s,color .15s,border-color .15s}
        .acc-hero__btn svg{width:16px;height:16px}
        .acc-hero__btn--ghost{border:1.5px solid rgba(249,168,212,.5);color:#fce7f3;background:rgba(255,255,255,.06)}
        .acc-hero__btn--ghost:hover{background:rgba(255,255,255,.12)}
        .acc-hero__btn--fill{background:#db2777;color:#fff;border:1.5px solid transparent}
        .acc-hero__btn--fill:hover{background:#be185d}

        .acc-shell{display:grid;grid-template-columns:minmax(0,240px) minmax(0,1fr);gap:clamp(1rem,2.5vw,1.5rem);align-items:start;margin-top:1.5rem}
        .acc-side{position:sticky;top:88px;width:100%;background:#fff;border:1px solid #f0e9f2;border-radius:1.25rem;padding:1rem;box-shadow:0 10px 30px rgba(45,27,38,.06)}
        .acc-nav{display:grid;gap:.3rem}
        .acc-nav a{display:flex;align-items:center;gap:.75rem;min-height:46px;padding:.6rem .85rem;border-radius:.75rem;color:#4b4453;text-decoration:none;font-size:.9rem;font-weight:600;transition:background .15s,color .15s}
        .acc-nav a svg{width:19px;height:19px;stroke:#be185d;flex-shrink:0}
        .acc-nav a:hover{background:#fdf2f8;color:#17121a}
        .acc-nav a.active{background:#be185d;color:#fff}
        .acc-nav a.active svg{stroke:#fff}
        .acc-count{margin-left:auto;font-size:.7rem;background:#fdf2f8;color:#be185d;border-radius:999px;padding:.15rem .5rem;font-weight:800}
        .acc-nav a.active .acc-count{background:rgba(255,255,255,.25);color:#fff}
        .acc-logout-form{margin-top:.75rem;padding-top:.85rem;border-top:1px solid #f0e9f2}
        .acc-logout{width:100%;min-height:46px;display:flex;align-items:center;justify-content:center;gap:.6rem;border:1.5px solid #f0e9f2;background:#fff;color:#be185d;border-radius:.75rem;padding:.65rem;font-weight:700;cursor:pointer;font-family:inherit;font-size:.9rem;transition:background .15s,color .15s,border-color .15s}
        .acc-logout:hover{background:#fee2e2;border-color:#fda4af;color:#9f1239}
        .acc-logout svg{width:17px;height:17px}

        .acc-main{min-width:0}
        .acc-card{background:#fff;border:1px solid #f0e9f2;border-radius:1.25rem;padding:1.5rem;box-shadow:0 10px 30px rgba(45,27,38,.06)}
        .acc-card+.acc-card{margin-top:1rem}
        .acc-title{display:flex;align-items:center;justify-content:space-between;gap:1rem;margin:0 0 1.25rem}
        .acc-title h1{font-size:1.3rem;margin:0;color:#17121a}
        .acc-stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:1rem}
        .acc-stat{border:1px solid #f0e9f2;border-radius:1rem;padding:1.1rem 1.2rem;background:linear-gradient(180deg,#fff,#fdf2f8);display:flex;align-items:center;gap:.9rem}
        .acc-stat__icon{width:42px;height:42px;border-radius:.85rem;background:#fdf2f8;display:grid;place-items:center;color:#be185d;flex-shrink:0}
        .acc-stat__icon svg{width:21px;height:21px}
        .acc-stat__label span{font-size:.78rem;color:#6b7280;font-weight:600;display:block}
        .acc-stat__label strong{font-size:1.55rem;color:#831843;line-height:1.1;display:block}
        .acc-sub{font-size:1.05rem;font-weight:800;color:#17121a;margin:1.5rem 0 .9rem}
        .order-card{border:1px solid #f0e9f2;border-radius:1rem;background:#fff;padding:1.25rem;margin-bottom:1rem}
        .order-head,.order-meta{display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap}
        .status-pill{display:inline-flex;padding:.35rem .7rem;border-radius:999px;background:#eee7df;color:#64472f;font-size:.75rem;font-weight:700;text-transform:capitalize}
        .status-pill.s-awaiting{background:#fef3c7;color:#92400e}
        .status-pill.s-processing{background:#dbeafe;color:#1e40af}
        .status-pill.s-confirmed{background:#fce7f3;color:#9d174d}
        .status-pill.s-waiting_for_confirmation{background:#fef3c7;color:#92400e}
        .status-pill.s-shipped{background:#ede9fe;color:#5b21b6}
        .status-pill.s-in_transit{background:#cffafe;color:#155e75}
        .status-pill.s-delivered{background:#dcfce7;color:#166534}
        .status-pill.s-cancelled{background:#fee2e2;color:#991b1b}
        .order-progress{display:grid;grid-template-columns:repeat(6,1fr);gap:.35rem;margin:1rem 0}
        .order-progress span{height:.32rem;border-radius:999px;background:#e5e7eb}
        .order-progress span.done{background:linear-gradient(90deg,#db2777,#f472b6)}
        .order-items{color:#6b7280;font-size:.9rem;margin-top:.75rem}
        .tracking-link{font-weight:700;color:#be185d;text-decoration:underline}
        .acc-empty{color:#6b7280;padding:1.5rem;text-align:center;border:1px dashed #f0e9f2;border-radius:1rem}
        .addr-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:1rem}
        .addr-card{border:1px solid #f0e9f2;border-radius:1rem;padding:1.1rem;background:#fff;position:relative}
        .addr-badge{position:absolute;top:.8rem;right:.8rem;font-size:.65rem;font-weight:800;letter-spacing:.06em;background:#fdf2f8;color:#be185d;border-radius:999px;padding:.2rem .55rem}
        .addr-card strong{color:#17121a}
        .addr-card p{color:#6b7280;font-size:.85rem;line-height:1.6;margin:.35rem 0 0}
        .ntf{display:flex;gap:.9rem;padding:1rem 0;border-bottom:1px solid #f0e9f2;align-items:flex-start}
        .ntf:last-child{border-bottom:0}
        .ntf-dot{width:36px;height:36px;border-radius:50%;flex-shrink:0;display:grid;place-items:center;background:#fdf2f8;color:#be185d}
        .ntf-dot svg{width:17px;height:17px}
        .ntf b{color:#17121a;font-size:.9rem}
        .ntf p{color:#6b7280;font-size:.85rem;margin:.15rem 0 0}
        .ntf time{display:block;font-size:.72rem;color:#9ca3af;margin-top:.15rem}
        .sup-grid{display:grid;gap:.9rem}
        .sup-row{display:flex;align-items:center;gap:.9rem;padding:.9rem;border:1px solid #f0e9f2;border-radius:.9rem;background:#fff;text-decoration:none;color:#17121a;transition:border-color .15s}
        .sup-row:hover{border-color:#f9a8d4}
        .sup-row svg{width:20px;height:20px;stroke:#be185d;flex-shrink:0}
        .sup-row span{display:block;font-size:.78rem;color:#6b7280}
        .sup-row strong{font-size:.92rem}
        .quick-links{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:.75rem}
        .quick{display:flex;align-items:center;gap:.6rem;border:1px solid #f0e9f2;border-radius:.9rem;padding:.85rem 1rem;background:#fff;color:#17121a;text-decoration:none;font-weight:700;font-size:.85rem;transition:border-color .15s,background .15s}
        .quick:hover{border-color:#f9a8d4;background:#fdf2f8}
        .quick svg{width:17px;height:17px;stroke:#be185d;flex-shrink:0}
        .acc-note{font-size:.8rem;color:#6b7280}
        @media(max-width:991px){.acc{padding:1.5rem 1rem 3rem}.acc-hero{padding:1rem}.acc-hero__avatar{width:52px;height:52px;font-size:1.1rem}.acc-shell{grid-template-columns:minmax(0,1fr);gap:1rem;margin-top:1rem}.acc-side{position:static;top:auto;padding:.6rem}.acc-nav{display:grid;grid-template-columns:minmax(0,1fr);gap:.3rem;padding:.1rem}.acc-nav a{min-height:44px;padding:.55rem .85rem}.acc-logout-form{margin-top:.5rem;padding-top:.75rem}}
        @media(max-width:640px){.acc-stats{grid-template-columns:1fr}.order-head,.order-meta{align-items:flex-start}.acc-card{padding:1.1rem}.order-card{padding:1rem}.acc-title h1{font-size:1.2rem}.acc-hero__meta{flex:1 1 100%}}
    </style>
    @php
        $statusLabels = [
            'awaiting' => 'Awaiting', 'processing' => 'Processing', 'confirmed' => 'Confirmed',
            'waiting_for_confirmation' => 'Waiting for Confirmation', 'shipped' => 'Shipped',
            'in_transit' => 'In Transit', 'delivered' => 'Delivered', 'cancelled' => 'Cancelled',
        ];
        $steps = ['awaiting', 'processing', 'confirmed', 'shipped', 'in_transit', 'delivered'];
        $statusLabel = fn ($s) => $statusLabels[$s] ?? \Illuminate\Support\Str::headline((string) $s);
        $initials = strtoupper(substr(trim((string) $user->name), 0, 1));
        $waNumber = preg_replace('/\D/', '', (string) ($support['whatsapp'] ?? ''));
        $pic = $user->profile_pic;
        if ($pic && !preg_match('#^https?://#i', (string) $pic)) {
            $pic = asset('storage/'.$pic);
        }
    @endphp
    <div class="acc">
        <header class="acc-hero">
            <div class="acc-hero__avatar">
                @if($pic)<img src="{{ $pic }}" alt="{{ $user->name }}" loading="lazy">@else{{ $initials }}@endif
            </div>
            <div class="acc-hero__meta">
                <h1>{{ $user->name }}</h1>
                <p>{{ $user->email }}</p>
            </div>
            <div class="acc-hero__actions">
                <a class="acc-hero__btn acc-hero__btn--ghost" href="{{ route('profile.edit', ['tab' => 'settings']) }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>Edit profile</a>
                <a class="acc-hero__btn acc-hero__btn--fill" href="{{ route('products.index') }}">Continue shopping</a>
            </div>
        </header>

        <div class="acc-shell">
            <aside class="acc-side">
                <nav class="acc-nav" aria-label="Account navigation">
                    <a href="{{ route('profile.edit', ['tab' => 'overview']) }}" class="{{ $tab === 'overview' ? 'active' : '' }}" aria-current="{{ $tab === 'overview' ? 'page' : 'false' }}"><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>Overview</a>
                    <a href="{{ route('profile.edit', ['tab' => 'orders']) }}" class="{{ $tab === 'orders' ? 'active' : '' }}" aria-current="{{ $tab === 'orders' ? 'page' : 'false' }}"><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>My Orders{!! $allOrders->isNotEmpty() ? '<span class="acc-count">'.$allOrders->count().'</span>' : '' !!}</a>
                    <a href="{{ route('profile.edit', ['tab' => 'addresses']) }}" class="{{ $tab === 'addresses' ? 'active' : '' }}" aria-current="{{ $tab === 'addresses' ? 'page' : 'false' }}"><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>Addresses{!! $addresses->isNotEmpty() ? '<span class="acc-count">'.$addresses->count().'</span>' : '' !!}</a>
                    <a href="{{ route('profile.edit', ['tab' => 'notifications']) }}" class="{{ $tab === 'notifications' ? 'active' : '' }}" aria-current="{{ $tab === 'notifications' ? 'page' : 'false' }}"><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>Notifications{!! $notifications->isNotEmpty() ? '<span class="acc-count">'.$notifications->count().'</span>' : '' !!}</a>
                    <a href="{{ route('profile.edit', ['tab' => 'settings']) }}" class="{{ $tab === 'settings' ? 'active' : '' }}" aria-current="{{ $tab === 'settings' ? 'page' : 'false' }}"><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1Z"/></svg>Account Settings</a>
                    <a href="{{ route('profile.edit', ['tab' => 'support']) }}" class="{{ $tab === 'support' ? 'active' : '' }}" aria-current="{{ $tab === 'support' ? 'page' : 'false' }}"><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>Support</a>
                </nav>
                <form method="POST" action="{{ route('logout') }}" class="acc-logout-form">
                    @csrf
                    <button type="submit" class="acc-logout"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/></svg>Logout</button>
                </form>
            </aside>

            <section class="acc-main">
                @if($tab === 'overview')
                <div class="acc-card">
                    <div class="acc-title"><h1>Welcome back, {{ $user->name }}</h1><a class="acc-note tracking-link" href="{{ route('products.index') }}">Continue shopping →</a></div>
                    <div class="acc-stats">
                        <div class="acc-stat"><div class="acc-stat__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="M3.3 7 12 12l8.7-5"/><path d="M12 22V12"/></svg></div><div class="acc-stat__label"><span>Active orders</span><strong>{{ $orderStats['active'] }}</strong></div></div>
                        <div class="acc-stat"><div class="acc-stat__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg></div><div class="acc-stat__label"><span>Delivered</span><strong>{{ $orderStats['delivered'] }}</strong></div></div>
                        <div class="acc-stat"><div class="acc-stat__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg></div><div class="acc-stat__label"><span>Total orders</span><strong>{{ $orderStats['total'] }}</strong></div></div>
                    </div>
                    <h3 class="acc-sub">Recent orders</h3>
                    @forelse($recentOrders->take(3) as $order)
                        @php $stepIndex = array_search($order->order_status, $steps, true); @endphp
                        <article class="order-card">
                            <div class="order-head"><div><strong>{{ $order->order_number ?: '#'.$order->id }}</strong><div class="text-sm text-gray-500" style="color:#6b7280;font-size:.8rem">Placed {{ $order->created_at ? \Illuminate\Support\Carbon::parse($order->created_at)->format('d M Y, g:i A') : '—' }}</div></div><span class="status-pill s-{{ $order->order_status }}">{{ $statusLabel($order->order_status) }}</span></div>
                            <div class="order-progress" title="Order progress">@foreach($steps as $index=>$step)<span class="{{ $stepIndex !== false && $index <= $stepIndex ? 'done' : '' }}"></span>@endforeach</div>
                            <div class="order-items">{{ $order->items->pluck('product_name')->filter()->join(', ') }} · <strong>৳{{ number_format((float)$order->total_amount,0) }}</strong></div>
                        </article>
                    @empty
                        <div class="acc-empty">You have no orders yet. <a class="tracking-link" href="{{ route('products.index') }}">Start shopping</a></div>
                    @endforelse
                    <h3 class="acc-sub">Quick links</h3>
                    <div class="quick-links">
                        <a class="quick" href="{{ route('profile.edit', ['tab' => 'orders']) }}"><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/></svg>All orders</a>
                        <a class="quick" href="{{ route('profile.edit', ['tab' => 'addresses']) }}"><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>My addresses</a>
                        <a class="quick" href="{{ route('profile.edit', ['tab' => 'notifications']) }}"><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/></svg>Notifications</a>
                        <a class="quick" href="{{ route('profile.edit', ['tab' => 'settings']) }}"><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1Z"/></svg>Account settings</a>
                        <a class="quick" href="{{ route('profile.edit', ['tab' => 'support']) }}"><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>Get support</a>
                    </div>
                </div>

                @elseif($tab === 'orders')
                <div class="acc-card">
                    <div class="acc-title"><h1>Order history</h1><span class="acc-note">{{ $allOrders->count() }} order(s)</span></div>
                    @forelse($allOrders as $order)
                        @php $stepIndex = array_search($order->order_status, $steps, true); @endphp
                        <article class="order-card">
                            <div class="order-head"><div><strong>{{ $order->order_number ?: '#'.$order->id }}</strong><div style="color:#6b7280;font-size:.8rem">Placed {{ $order->created_at ? \Illuminate\Support\Carbon::parse($order->created_at)->format('d M Y, g:i A') : '—' }}</div></div><span class="status-pill s-{{ $order->order_status }}">{{ $statusLabel($order->order_status) }}</span></div>
                            @if(in_array($order->order_status, $steps, true))
                            <div class="order-progress" title="Order progress">@foreach($steps as $index=>$step)<span class="{{ $stepIndex !== false && $index <= $stepIndex ? 'done' : '' }}"></span>@endforeach</div>
                            @endif
                            <div class="order-meta"><span>{{ $order->items->sum('quantity') }} item(s) · <strong>৳{{ number_format((float)$order->total_amount,0) }}</strong></span><span>Payment: {{ \Illuminate\Support\Str::headline($order->payment_status) }} @if($order->payment_method) · {{ $order->payment_method }}@endif</span>@if($order->tracking_url)<a class="tracking-link" href="{{ $order->tracking_url }}" target="_blank" rel="noopener">Track shipment</a>@elseif($order->tracking_number)<span>Tracking: {{ $order->tracking_number }}</span>@endif</div>
                            <div class="order-items">{{ $order->items->pluck('product_name')->filter()->join(', ') }}</div>
                        </article>
                    @empty
                        <div class="acc-empty">No orders yet. <a class="tracking-link" href="{{ route('products.index') }}">Start shopping</a></div>
                    @endforelse
                </div>

                @elseif($tab === 'addresses')
                <div class="acc-card">
                    <div class="acc-title"><h1>My addresses</h1></div>
                    @if($addresses->isNotEmpty())
                        <div class="addr-grid">
                            @foreach($addresses as $index => $address)
                                <div class="addr-card">
                                    @if($index === 0)<span class="addr-badge">Default</span>@endif
                                    <strong>{{ $address->customer_name ?: $user->name }}</strong>
                                    <p>
                                        @if($address->phone)
                                            {{ $address->phone }}<br>
                                        @endif
                                        {{ $address->shipping_address }}<br>{{ $address->thana }}{{ $address->post_office ? ', '.$address->post_office : '' }}{{ $address->district ? ', '.$address->district : '' }}{{ $address->post_code ? ' — '.$address->post_code : '' }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                        <p class="acc-note" style="margin-top:1rem">Addresses are collected from your recent deliveries. Update them the next time you place an order.</p>
                    @else
                        <div class="acc-empty">No saved addresses yet. Your delivery addresses will appear here after your first order.</div>
                    @endif
                </div>

                @elseif($tab === 'notifications')
                <div class="acc-card">
                    <div class="acc-title"><h1>Notifications</h1></div>
                    @forelse($notifications as $n)
                        <div class="ntf">
                            <div class="ntf-dot"><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg></div>
                            <div><b>Order {{ $n->order?->order_number ?: '#'.($n->order_id) }} — {{ $statusLabel($n->to_status) }}</b><p>Your order status has been updated to {{ $statusLabel($n->to_status) }}.@if($n->note) {{ $n->note }}@endif</p>@if($n->created_at)<time>{{ \Illuminate\Support\Carbon::parse($n->created_at)->format('d M Y, g:i A') }}</time>@endif</div>
                        </div>
                    @empty
                        <div class="acc-empty">You have no notifications yet. You'll be notified here when your orders change status.</div>
                    @endforelse
                </div>

                @elseif($tab === 'settings')
                <div class="acc-card">
                    <div class="acc-title"><h1>Account settings</h1></div>
                    <div class="max-w-xl">@include('profile.partials.update-profile-information-form')</div>
                </div>
                <div class="acc-card">
                    <div class="max-w-xl">@include('profile.partials.update-password-form')</div>
                </div>
                <div class="acc-card">
                    <div class="max-w-xl">@include('profile.partials.delete-user-form')</div>
                </div>

                @elseif($tab === 'support')
                <div class="acc-card">
                    <div class="acc-title"><h1>Support &amp; help</h1></div>
                    <p style="color:#6b7280;margin:0 0 1.25rem">Questions about an order, delivery, or a product? Our team is here to help you.</p>
                    <div class="sup-grid">
                        @if(!empty($waNumber))
                        <a class="sup-row" href="https://wa.me/{{ $waNumber }}?text={{ urlencode('Hi, I need help with my order.') }}" target="_blank" rel="noopener noreferrer">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5Z"/></svg>
                            <div><span>WhatsApp (fastest)</span><strong>Chat with us</strong></div>
                        </a>
                        @endif
                        @if(!empty($support['phone']))
                        <a class="sup-row" href="tel:{{ preg_replace('/\D/', '', $support['phone']) }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92Z"/></svg>
                            <div><span>Call us</span><strong>{{ $support['phone'] }}</strong></div>
                        </a>
                        @endif
                        @if(!empty($support['email']))
                        <a class="sup-row" href="mailto:{{ $support['email'] }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                            <div><span>Email us</span><strong>{{ $support['email'] }}</strong></div>
                        </a>
                        @endif
                        @if(!empty($support['address']))
                        <div class="sup-row" style="cursor:default">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                            <div><span>Store address</span><strong>{{ $support['address'] }}</strong></div>
                        </div>
                        @endif
                        @if(!empty($support['facebook']) || !empty($support['instagram']))
                        <div style="display:flex;gap:.75rem;flex-wrap:wrap">
                            @if(!empty($support['facebook']))<a class="sup-row" href="{{ $support['facebook'] }}" target="_blank" rel="noopener noreferrer" style="flex:1;min-width:180px"><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg><div><span>Facebook</span><strong>Follow us</strong></div></a>@endif
                            @if(!empty($support['instagram']))<a class="sup-row" href="{{ $support['instagram'] }}" target="_blank" rel="noopener noreferrer" style="flex:1;min-width:180px"><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="20" x="2" y="2" rx="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg><div><span>Instagram</span><strong>Follow us</strong></div></a>@endif
                        </div>
                        @endif
                    </div>
                    <p class="acc-note" style="margin-top:1rem">{{ $support['store_name'] }} — we usually respond within a few hours during business time.</p>
                </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>

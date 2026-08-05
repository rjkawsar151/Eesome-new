<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">My account</h2></x-slot>
    <style>
        .account-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:1rem}.account-stat,.order-card{border:1px solid #e8e1da;border-radius:1rem;background:#fff}.account-stat{padding:1.25rem}.account-stat strong{display:block;font-size:1.8rem;color:#37271e}.order-card{padding:1.25rem}.order-head,.order-meta{display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap}.status-pill{display:inline-flex;padding:.35rem .7rem;border-radius:999px;background:#eee7df;color:#64472f;font-size:.75rem;font-weight:700;text-transform:capitalize}.status-pill.active{background:#e8f3eb;color:#24633b}.order-progress{display:grid;grid-template-columns:repeat(6,1fr);gap:.35rem;margin:1rem 0}.order-progress span{height:.32rem;border-radius:999px;background:#e5e7eb}.order-progress span.done{background:#9a6745}.order-items{color:#6b625c;font-size:.9rem;margin-top:.75rem}.tracking-link{font-weight:700;color:#855839;text-decoration:underline}.section-title{font-size:1.25rem;font-weight:700;color:#30231c;margin-bottom:1rem}@media(max-width:700px){.account-grid{grid-template-columns:1fr}.order-head,.order-meta{align-items:flex-start}.order-card{padding:1rem}}
    </style>
    <div class="py-10"><div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-7">
        <div><h1 class="text-3xl font-bold text-gray-900">Welcome, {{ $user->name }}</h1><p class="mt-1 text-gray-600">Track orders and manage your account details.</p></div>
        <section class="account-grid" aria-label="Order summary">
            <div class="account-stat"><span class="text-sm text-gray-500">Active orders</span><strong>{{ $orderStats['active'] }}</strong></div>
            <div class="account-stat"><span class="text-sm text-gray-500">Delivered</span><strong>{{ $orderStats['delivered'] }}</strong></div>
            <div class="account-stat"><span class="text-sm text-gray-500">Total orders</span><strong>{{ $orderStats['total'] }}</strong></div>
        </section>
        <section><h2 class="section-title">Active orders</h2><div class="space-y-4">
            @forelse($activeOrders as $order)
                @php
                    $steps=['awaiting','processing','confirmed','shipped','in_transit','delivered'];
                    $stepIndex=array_search($order->order_status,$steps,true);
                @endphp
                <article class="order-card">
                    <div class="order-head"><div><strong>{{ $order->order_number ?: '#'.$order->id }}</strong><div class="text-sm text-gray-500">Placed {{ $order->created_at ? \Illuminate\Support\Carbon::parse($order->created_at)->format('d M Y, g:i A') : '—' }}</div></div><span class="status-pill active">{{ \Illuminate\Support\Str::headline($order->order_status) }}</span></div>
                    <div class="order-progress" title="Order progress">@foreach($steps as $index=>$step)<span class="{{ $stepIndex !== false && $index <= $stepIndex ? 'done' : '' }}"></span>@endforeach</div>
                    <div class="order-meta"><span>{{ $order->items->sum('quantity') }} item(s) · <strong>৳{{ number_format((float)$order->total_amount,0) }}</strong></span><span>Payment: {{ \Illuminate\Support\Str::headline($order->payment_status) }}</span>@if($order->tracking_url)<a class="tracking-link" href="{{ $order->tracking_url }}" target="_blank" rel="noopener">Track shipment</a>@elseif($order->tracking_number)<span>Tracking: {{ $order->tracking_number }}</span>@endif</div>
                    <div class="order-items">{{ $order->items->pluck('product_name')->filter()->join(', ') }}</div>
                </article>
            @empty<div class="order-card text-gray-600">You have no active orders right now.</div>@endforelse
        </div></section>
        <section><h2 class="section-title">Recent order history</h2><div class="order-card overflow-x-auto"><table class="min-w-full text-sm"><thead><tr class="text-left text-gray-500"><th class="py-2 pr-5">Order</th><th class="py-2 pr-5">Date</th><th class="py-2 pr-5">Status</th><th class="py-2 text-right">Total</th></tr></thead><tbody>@forelse($recentOrders as $order)<tr class="border-t border-gray-100"><td class="py-3 pr-5 font-semibold">{{ $order->order_number ?: '#'.$order->id }}</td><td class="py-3 pr-5">{{ $order->created_at ? \Illuminate\Support\Carbon::parse($order->created_at)->format('d M Y') : '—' }}</td><td class="py-3 pr-5"><span class="status-pill">{{ \Illuminate\Support\Str::headline($order->order_status) }}</span></td><td class="py-3 text-right font-semibold">৳{{ number_format((float)$order->total_amount,0) }}</td></tr>@empty<tr><td colspan="4" class="py-5 text-gray-500">No orders yet.</td></tr>@endforelse</tbody></table></div></section>
        <div class="grid lg:grid-cols-2 gap-6"><div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg"><div class="max-w-xl">@include('profile.partials.update-profile-information-form')</div></div><div class="space-y-6"><div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg"><div class="max-w-xl">@include('profile.partials.update-password-form')</div></div><div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg"><div class="max-w-xl">@include('profile.partials.delete-user-form')</div></div></div></div>
    </div></div>
</x-app-layout>
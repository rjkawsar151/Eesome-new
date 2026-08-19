@extends('layouts.admin')
@section('title', 'Order ' . ($order->order_number ?: '#' . $order->id))
@section('heading', 'Order details')

@push('styles')
<style>
/* Main 2-column layout */
.order-layout {
    display: grid;
    grid-template-columns: minmax(0, 1.85fr) minmax(320px, 1.15fr);
    gap: 1.25rem;
    align-items: start;
    margin-top: 1.25rem;
}
.order-main-col, .order-side-col {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

/* Card Styling */
.order-card {
    background: #fff;
    border: 1px solid var(--line);
    border-radius: .85rem;
    padding: 1.25rem;
    box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
}
.order-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: .85rem;
    margin-bottom: 1rem;
    border-bottom: 1px solid var(--line);
}
.order-card-title {
    font-size: 1.05rem;
    font-weight: 800;
    color: var(--ink);
    margin: 0;
    display: flex;
    align-items: center;
    gap: .5rem;
}
.order-card-title svg {
    width: 18px;
    height: 18px;
    color: var(--brand);
    flex-shrink: 0;
}

/* Ordered Items List (Clean horizontal rows with square thumbnails) */
.order-items-list {
    display: flex;
    flex-direction: column;
    gap: .75rem;
}
.order-item-row {
    display: grid;
    grid-template-columns: 56px minmax(180px, 1fr) auto auto auto;
    align-items: center;
    gap: 1rem;
    padding: .85rem;
    background: #fdfdfe;
    border: 1px solid var(--line);
    border-radius: .75rem;
    transition: border-color .15s ease, background-color .15s ease;
}
.order-item-row:hover {
    border-color: #cbd5e1;
    background: #f8fafc;
}
.order-item-thumb-box {
    width: 56px;
    height: 56px;
    min-width: 56px;
    border-radius: .6rem;
    border: 1px solid #e2e8f0;
    background: #fff;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2px;
}
.order-item-thumb-img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: .45rem;
}
.order-item-info {
    display: flex;
    flex-direction: column;
    gap: .25rem;
    min-width: 0;
}
.order-item-name {
    font-size: .92rem;
    font-weight: 700;
    color: var(--ink);
    text-decoration: none;
    line-height: 1.35;
    word-break: break-word;
}
.order-item-name:hover {
    color: var(--brand);
}
.order-item-meta-badges {
    display: flex;
    align-items: center;
    gap: .5rem;
    flex-wrap: wrap;
    font-size: .8rem;
    color: var(--muted);
}
.order-color-pill {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .15rem .45rem;
    background: #f1f5f9;
    border-radius: 999px;
    font-size: .75rem;
    font-weight: 600;
    color: #334155;
}
.order-swatch-dot {
    width: 11px;
    height: 11px;
    border-radius: 50%;
    border: 1px solid rgba(0,0,0,0.2);
    flex-shrink: 0;
}
.order-sku-pill {
    font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
    font-size: .75rem;
    padding: .15rem .45rem;
    background: #f1f5f9;
    border-radius: 4px;
    color: #475569;
}
.order-item-qty {
    font-size: .85rem;
    font-weight: 700;
    padding: .25rem .6rem;
    background: #f1f5f9;
    color: #334155;
    border-radius: 999px;
    white-space: nowrap;
    text-align: center;
}
.order-item-price {
    font-size: .88rem;
    color: var(--muted);
    white-space: nowrap;
    text-align: right;
}
.order-item-total {
    font-size: .95rem;
    font-weight: 800;
    color: var(--ink);
    white-space: nowrap;
    text-align: right;
    min-width: 75px;
}

/* Order Financial Summary */
.order-fin-summary {
    margin-top: 1.25rem;
    padding: 1.1rem;
    background: #f8fafc;
    border: 1px solid var(--line);
    border-radius: .75rem;
    display: flex;
    flex-direction: column;
    gap: .55rem;
}
.order-fin-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: .9rem;
    color: var(--muted);
}
.order-fin-row.discount {
    color: #166534;
    font-weight: 600;
}
.order-fin-row.grand-total {
    border-top: 1px solid var(--line);
    padding-top: .75rem;
    margin-top: .35rem;
    font-size: 1.2rem;
    font-weight: 800;
    color: var(--brand);
}

/* Sidebar Details Grid */
.detail-list {
    display: flex;
    flex-direction: column;
    gap: .75rem;
    font-size: .9rem;
}
.detail-item {
    display: flex;
    flex-direction: column;
    gap: .15rem;
}
.detail-label {
    font-size: .78rem;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: var(--muted);
    font-weight: 700;
}
.detail-value {
    color: var(--ink);
    font-weight: 600;
}
.address-box {
    background: #f8fafc;
    border: 1px solid var(--line);
    border-radius: .65rem;
    padding: .85rem;
    margin-top: .35rem;
    font-size: .88rem;
    line-height: 1.5;
    color: #334155;
}

/* Status Timeline */
.timeline {
    position: relative;
    padding-left: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}
.timeline::before {
    content: '';
    position: absolute;
    top: .4rem;
    bottom: .4rem;
    left: 6px;
    width: 2px;
    background: #e2e8f0;
}
.timeline-item {
    position: relative;
}
.timeline-dot {
    position: absolute;
    left: -1.5rem;
    top: .25rem;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: var(--brand);
    border: 3px solid #fff;
    box-shadow: 0 0 0 1px var(--line);
}
.timeline-title {
    font-size: .88rem;
    font-weight: 700;
    color: var(--ink);
    margin: 0;
}
.timeline-meta {
    font-size: .78rem;
    color: var(--muted);
    margin-top: .15rem;
}
.timeline-note {
    margin-top: .4rem;
    font-size: .84rem;
    background: #f8fafc;
    border: 1px solid var(--line);
    border-radius: .5rem;
    padding: .45rem .65rem;
    color: #475569;
}

/* Status pill styling */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .35rem .8rem;
    border-radius: 999px;
    font-size: .82rem;
    font-weight: 700;
    text-transform: capitalize;
}
.status-badge.s-pending, .status-badge.s-awaiting, .status-badge.s-waiting_for_confirmation {
    background: #fef3c7;
    color: #92400e;
}
.status-badge.s-processing, .status-badge.s-confirmed {
    background: #e0f2fe;
    color: #0369a1;
}
.status-badge.s-shipped, .status-badge.s-in_transit {
    background: #f3e8ff;
    color: #6b21a8;
}
.status-badge.s-delivered {
    background: #dcfce7;
    color: #15803d;
}
.status-badge.s-cancelled, .status-badge.s-failed, .status-badge.s-refunded {
    background: #fee2e2;
    color: #b91c1c;
}

/* Responsive */
@media (max-width: 960px) {
    .order-layout {
        grid-template-columns: 1fr;
    }
}
@media (max-width: 640px) {
    .order-item-row {
        grid-template-columns: 48px minmax(0, 1fr);
        gap: .75rem;
    }
    .order-item-thumb-box {
        width: 48px;
        height: 48px;
        min-width: 48px;
    }
    .order-item-row-mobile-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        grid-column: 1 / -1;
        padding-top: .6rem;
        border-top: 1px dashed var(--line);
        margin-top: .2rem;
    }
}
</style>
@endpush

@section('content')
{{-- Top Page Header --}}
<div class="page-head" style="align-items:start">
    <div>
        <div style="display:flex;align-items:center;gap:.6rem;flex-wrap:wrap">
            <h1 class="title">{{ $order->order_number ?: '#'.$order->id }}</h1>
            <span class="status-badge s-{{ $order->order_status }}">
                {{ \Illuminate\Support\Str::headline($order->order_status) }}
            </span>
        </div>
        <p class="subtle" style="margin:.35rem 0 0">
            Placed on <strong>{{ $order->created_at?->format('d M Y, g:i A') }}</strong>
            @if($order->payment_method) · via <strong>{{ $order->payment_method }}</strong> @endif
        </p>
    </div>
    <div class="page-head-actions" style="display:flex;align-items:center;gap:.6rem">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-soft btn-sm">← Back to orders</a>
    </div>
</div>

{{-- Main Grid --}}
<div class="order-layout">
    {{-- Left Main Column --}}
    <div class="order-main-col">
        {{-- Ordered Items Section --}}
        <section class="order-card">
            <div class="order-card-header">
                <h2 class="order-card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                    <span>Ordered Items</span>
                </h2>
                <span class="subtle" style="font-size:.85rem;font-weight:600">{{ $order->items->sum('quantity') }} item(s)</span>
            </div>

            <div class="order-items-list">
                @foreach($order->items as $item)
                <div class="order-item-row">
                    {{-- 1. Square Thumbnail --}}
                    @if($item->product)
                        <a href="{{ route('products.show', $item->product->slug ?? $item->product->id) }}" target="_blank" class="order-item-thumb-box" title="View item page">
                            <img src="{{ $item->resolved_image }}" onerror="this.onerror=null;this.src='{{ app(\App\Services\ProductImageResolver::class)->placeholder() }}'" alt="{{ $item->product_name }}" class="order-item-thumb-img" loading="lazy">
                        </a>
                    @else
                        <div class="order-item-thumb-box">
                            <img src="{{ $item->resolved_image }}" onerror="this.onerror=null;this.src='{{ app(\App\Services\ProductImageResolver::class)->placeholder() }}'" alt="{{ $item->product_name }}" class="order-item-thumb-img" loading="lazy">
                        </div>
                    @endif

                    {{-- 2. Product Name & Meta Badges --}}
                    <div class="order-item-info">
                        @if($item->product)
                            <a href="{{ route('products.show', $item->product->slug ?? $item->product->id) }}" target="_blank" class="order-item-name" title="View item page">{{ $item->product_name ?: 'Product' }} ↗</a>
                        @else
                            <span class="order-item-name">{{ $item->product_name ?: 'Legacy product' }}</span>
                        @endif


                        <div class="order-item-meta-badges">
                            @if($item->display_color)
                                <span class="order-color-pill">
                                    @if($item->selected_color_code)
                                        <span class="order-swatch-dot" style="background-color: {{ $item->selected_color_code }}"></span>
                                    @endif
                                    Color: {{ $item->display_color }}
                                </span>
                            @endif

                            @if($item->product_sku)
                                <code class="order-sku-pill">SKU: {{ $item->product_sku }}</code>
                            @endif
                        </div>
                    </div>

                    {{-- 3. Quantity --}}
                    <div>
                        <span class="order-item-qty">Qty: {{ $item->quantity }}</span>
                    </div>

                    {{-- 4. Unit Price --}}
                    <div class="order-item-price">
                        &#2547;{{ number_format((float)$item->price, 0) }}
                    </div>

                    {{-- 5. Line Total --}}
                    <div class="order-item-total">
                        &#2547;{{ number_format((float)($item->line_total ?: $item->price * $item->quantity), 0) }}
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Summary Breakdown --}}
            <div class="order-fin-summary">
                @php
                    $subtotal = $order->items->sum(fn($i) => (float)($i->line_total ?: $i->price * $i->quantity));
                    $shippingCost = (float)($order->shipping_cost ?? 0);
                    $discount = (float)($order->discount_amount ?? 0);
                @endphp
                <div class="order-fin-row">
                    <span>Items subtotal</span>
                    <strong style="color:var(--ink)">&#2547;{{ number_format($subtotal, 0) }}</strong>
                </div>
                @if($shippingCost > 0)
                <div class="order-fin-row">
                    <span>Shipping charge</span>
                    <strong style="color:var(--ink)">&#2547;{{ number_format($shippingCost, 0) }}</strong>
                </div>
                @else
                <div class="order-fin-row">
                    <span>Shipping charge</span>
                    <span style="color:#166534;font-weight:600">Free delivery</span>
                </div>
                @endif
                @if($discount > 0)
                <div class="order-fin-row discount">
                    <span>Discount @if($order->coupon_code)({{ $order->coupon_code }})@endif</span>
                    <span>-&#2547;{{ number_format($discount, 0) }}</span>
                </div>
                @endif
                <div class="order-fin-row grand-total">
                    <span>Total Amount</span>
                    <span>&#2547;{{ number_format((float)$order->total_amount, 0) }}</span>
                </div>
            </div>
        </section>

        {{-- Order Status History Timeline --}}
        <section class="order-card">
            <div class="order-card-header">
                <h2 class="order-card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span>Status History Timeline</span>
                </h2>
            </div>
            @if($order->statusHistories->isNotEmpty())
                <div class="timeline">
                    @foreach($order->statusHistories as $h)
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <h3 class="timeline-title">
                                {{ $h->from_status ? \Illuminate\Support\Str::headline($h->from_status) : 'Order Created' }} →
                                <span style="color:var(--brand)">{{ \Illuminate\Support\Str::headline($h->to_status) }}</span>
                            </h3>
                            <div class="timeline-meta">
                                {{ $h->created_at?->format('d M Y, g:i A') }}
                                @if($h->changedBy) · by <strong>{{ $h->changedBy->name }}</strong> @endif
                            </div>
                            @if($h->note)
                                <div class="timeline-note">{{ $h->note }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <p class="subtle" style="margin:0">No status history recorded yet.</p>
            @endif
        </section>
    </div>

    {{-- Right Sidebar Column --}}
    <aside class="order-side-col">
        {{-- Customer & Delivery Card --}}
        <section class="order-card">
            <div class="order-card-header">
                <h2 class="order-card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <span>Customer &amp; Delivery</span>
                </h2>
                @if($order->user)
                    <span class="badge badge-green">Registered</span>
                @else
                    <span class="badge badge-yellow">Guest Checkout</span>
                @endif
            </div>

            <div class="detail-list">
                <div class="detail-item">
                    <span class="detail-label">Customer Name</span>
                    <span class="detail-value" style="font-size:1rem">{{ $order->customer_name }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Phone Number</span>
                    <a href="tel:{{ $order->phone }}" class="detail-value" style="color:var(--brand);text-decoration:none;font-weight:700">{{ $order->phone }}</a>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Email Address</span>
                    <a href="mailto:{{ $order->email }}" class="detail-value" style="color:inherit;font-weight:500">{{ $order->email }}</a>
                </div>

                <hr style="border:0;border-top:1px solid var(--line);margin:.35rem 0">

                <div class="detail-item">
                    <span class="detail-label">Location</span>
                    <div style="display:flex;gap:.75rem;flex-wrap:wrap;margin-top:.2rem">
                        <span>Division: <strong>{{ $order->division ?: '—' }}</strong></span>
                        <span>District: <strong>{{ $order->district ?: '—' }}</strong></span>
                        <span>Thana: <strong>{{ $order->thana ?: '—' }}</strong></span>
                    </div>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Shipping Address</span>
                    <div class="address-box">
                        {{ $order->shipping_address }}
                        @if($order->post_office || $order->post_code)
                            <div style="color:var(--muted);font-size:.8rem;margin-top:.35rem">
                                Post Office: {{ $order->post_office ?: '—' }} {{ $order->post_code ? '('.$order->post_code.')' : '' }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        {{-- Payment Card --}}
        <section class="order-card">
            <div class="order-card-header">
                <h2 class="order-card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                    <span>Payment Information</span>
                </h2>
                <span class="status-badge s-{{ $order->payment_status }}">
                    {{ \Illuminate\Support\Str::headline($order->payment_status) }}
                </span>
            </div>

            @if(!empty($order->transaction_id))
                <div style="margin-bottom:1rem;padding:.85rem;border-radius:.6rem;background:#f0fdf4;border:1px solid #bbf7d0;color:#166534">
                    <div style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;font-weight:800;color:#15803d">Customer Transaction ID</div>
                    <div style="font-size:1.15rem;font-weight:800;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;letter-spacing:.04em;margin-top:.2rem;color:#14532d;word-break:break-all">{{ $order->transaction_id }}</div>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.orders.updatePayment', $order) }}">
                @csrf
                <div class="field">
                    <label>Payment Method</label>
                    <input class="input" value="{{ $order->payment_method ?: 'Cash on Delivery' }}" disabled style="background:#f8fafc">
                </div>

                <div class="field" style="margin-top:.75rem">
                    <label>Payment Status</label>
                    <select class="select" name="payment_status">
                        @foreach(['unpaid','pending','paid','partially_paid','failed','refunded','partially_refunded'] as $s)
                            <option value="{{ $s }}" @selected($order->payment_status === $s)>{{ \Illuminate\Support\Str::headline($s) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field" style="margin-top:.75rem">
                    <label>Transaction ID / Reference</label>
                    <input class="input" name="transaction_id" value="{{ $order->transaction_id }}" placeholder="e.g. TRX12345678">
                </div>

                <button class="btn btn-primary" style="margin-top:1rem;width:100%">Save Payment Changes</button>
            </form>
        </section>

        {{-- Fulfillment & Status Update Card --}}
        @if(count($allowedNext))
        <section class="order-card">
            <div class="order-card-header">
                <h2 class="order-card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                    <span>Update Fulfillment</span>
                </h2>
            </div>

            <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}">
                @csrf
                <div class="field">
                    <label>Next Order Status</label>
                    <select class="select" name="to_status">
                        @foreach($allowedNext as $s)
                            <option value="{{ $s }}">{{ \Illuminate\Support\Str::headline($s) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field" style="margin-top:.75rem">
                    <label>Courier / Shipping Provider</label>
                    <input class="input" name="shipping_provider" value="{{ $order->shipping_provider }}" placeholder="e.g. Steadfast, Pathao, RedX">
                </div>

                <div class="field" style="margin-top:.75rem">
                    <label>Tracking Number</label>
                    <input class="input" name="tracking_number" value="{{ $order->tracking_number }}" placeholder="e.g. SF12345678">
                </div>

                <div class="field" style="margin-top:.75rem">
                    <label>Tracking URL</label>
                    <input class="input" name="tracking_url" value="{{ $order->tracking_url }}" placeholder="https://...">
                </div>

                <div class="field" style="margin-top:.75rem">
                    <label>Estimated Delivery</label>
                    <input class="input" type="date" name="estimated_delivery_at" value="{{ $order->estimated_delivery_at }}">
                </div>

                <div class="field" style="margin-top:.75rem">
                    <label>Internal Audit Note</label>
                    <textarea class="textarea" name="note" rows="2" placeholder="Add an internal note about this status change..."></textarea>
                </div>

                <button class="btn btn-primary" style="margin-top:1rem;width:100%">Update Order Status</button>
            </form>
        </section>
        @endif

        {{-- Tracking Info Card (if available) --}}
        @if($order->tracking_number || $order->tracking_url)
        <section class="order-card" style="background:#f0fdf4;border-color:#bbf7d0">
            <div class="order-card-header" style="border-color:#bbf7d0">
                <h2 class="order-card-title" style="color:#166534">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                    <span>Shipment Tracking</span>
                </h2>
            </div>
            <div style="font-size:.9rem;color:#166534">
                <p style="margin:0 0 .5rem">
                    <strong>{{ $order->shipping_provider ?: 'Courier' }}:</strong> {{ $order->tracking_number }}
                </p>
                @if($order->tracking_url)
                    <a href="{{ $order->tracking_url }}" target="_blank" rel="noopener" class="btn btn-primary btn-sm" style="background:#166534;margin-top:.35rem">Track Shipment ↗</a>
                @endif
            </div>
        </section>
        @endif
    </aside>
</div>
@endsection
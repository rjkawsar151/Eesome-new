@extends('layouts.admin')
@section('title', 'Order ' . ($order->order_number ?: '#' . $order->id))
@section('heading', 'Order details')

@push('styles')
<style>
.order-items-table {
    width: 100%;
    border-collapse: collapse;
}
.order-items-table th {
    font-size: .75rem;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: var(--muted);
    padding: .75rem .6rem;
    border-bottom: 1px solid var(--line);
    text-align: left;
}
.order-items-table td {
    padding: .85rem .6rem;
    border-bottom: 1px solid var(--line);
    vertical-align: middle;
}
.order-prod-cell {
    display: flex;
    align-items: center;
    gap: .85rem;
    min-width: 220px;
}
.order-prod-thumb {
    width: 54px;
    height: 54px;
    min-width: 54px;
    border-radius: .55rem;
    border: 1px solid var(--line);
    background: #fff;
    object-fit: contain;
    padding: 2px;
}
.order-prod-info {
    display: flex;
    flex-direction: column;
    gap: .2rem;
}
.order-prod-name {
    font-size: .92rem;
    font-weight: 700;
    line-height: 1.3;
    color: var(--ink);
    text-decoration: none;
}
.order-prod-name:hover {
    color: var(--brand);
}
.order-prod-meta {
    display: flex;
    align-items: center;
    gap: .4rem;
    flex-wrap: wrap;
    font-size: .8rem;
    color: var(--muted);
}
.order-color-swatch {
    display: inline-block;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 1px solid rgba(0,0,0,0.15);
    vertical-align: middle;
}
.order-summary-box {
    margin-top: 1.25rem;
    padding: 1rem;
    background: #f8fafc;
    border: 1px solid var(--line);
    border-radius: .65rem;
    display: flex;
    flex-direction: column;
    gap: .45rem;
}
.order-summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: .9rem;
    color: var(--muted);
}
.order-summary-row.total {
    border-top: 1px solid var(--line);
    padding-top: .6rem;
    margin-top: .3rem;
    font-size: 1.15rem;
    font-weight: 800;
    color: var(--brand);
}
@media (max-width: 640px) {
    .order-items-table thead {
        display: none;
    }
    .order-items-table, .order-items-table tbody, .order-items-table tr, .order-items-table td {
        display: block;
        width: 100%;
    }
    .order-items-table tr {
        border: 1px solid var(--line);
        border-radius: .65rem;
        padding: .75rem;
        margin-bottom: .75rem;
        background: #fff;
    }
    .order-items-table td {
        padding: .35rem 0;
        border: 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .order-items-table td:first-child {
        display: block;
        padding-bottom: .6rem;
        border-bottom: 1px dashed var(--line);
        margin-bottom: .4rem;
    }
}
</style>
@endpush

@section('content')
<div class="page-head" style="align-items:start">
    <div>
        <h1 class="title">{{ $order->order_number ?: '#'.$order->id }}</h1>
        <p class="subtle">Placed on {{ $order->created_at?->format('d M Y, g:i A') }}</p>
    </div>
    <div class="page-head-actions" style="display:flex;align-items:center;gap:.6rem">
        <span class="badge" style="font-size:.85rem;padding:.35rem .75rem">{{ \Illuminate\Support\Str::headline($order->order_status) }}</span>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-soft btn-sm">← Back to orders</a>
    </div>
</div>

<div class="grid two-col" style="margin-top:1.5rem">
    <div class="grid">
        {{-- Items Section with Organized Thumbnails --}}
        <section class="card">
            <h2 style="margin:0 0 1rem;font-size:1.15rem;display:flex;align-items:center;justify-content:space-between">
                <span>Ordered Items</span>
                <span class="subtle" style="font-size:.85rem;font-weight:normal">{{ $order->items->sum('quantity') }} item(s)</span>
            </h2>
            <div class="table-wrap">
                <table class="order-items-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th style="text-align:center">Qty</th>
                            <th style="text-align:right">Price</th>
                            <th style="text-align:right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>
                                <div class="order-prod-cell">
                                    <img src="{{ $item->resolved_image }}" onerror="this.onerror=null;this.src='{{ app(\App\Services\ProductImageResolver::class)->placeholder() }}'\" alt=\"{{ $item->product_name }}\" class=\"order-prod-thumb\" loading=\"lazy\">
                                    <div class="order-prod-info">
                                        @if($item->product)
                                            <a href="{{ route('admin.products.edit', $item->product) }}" class="order-prod-name">{{ $item->product_name ?: 'Product' }}</a>
                                        @else
                                            <span class="order-prod-name">{{ $item->product_name ?: 'Legacy product' }}</span>
                                        @endif
                                        <div class="order-prod-meta">
                                            @if($item->display_color)
                                                <span>
                                                    @if($item->selected_color_code)
                                                        <span class="order-color-swatch" style="background-color: {{ $item->selected_color_code }}"></span>
                                                    @endif
                                                    Color: <strong>{{ $item->display_color }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td><code style="background:#f1f5f9;padding:.2rem .4rem;border-radius:4px;font-size:.8rem">{{ $item->product_sku ?: '—' }}</code></td>
                            <td style="text-align:center"><span class="badge" style="background:#f1f5f9;color:#334155;font-weight:700">{{ $item->quantity }}</span></td>
                            <td style="text-align:right;white-space:nowrap">&#2547;{{ number_format((float)$item->price, 0) }}</td>
                            <td style="text-align:right;white-space:nowrap;font-weight:700">&#2547;{{ number_format((float)($item->line_total ?: $item->price * $item->quantity), 0) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Summary Breakdown --}}
            <div class="order-summary-box">
                @php
                    $subtotal = $order->items->sum(fn($i) => (float)($i->line_total ?: $i->price * $i->quantity));
                    $shippingCost = (float)($order->shipping_cost ?? 0);
                    $discount = (float)($order->discount_amount ?? 0);
                @endphp
                <div class="order-summary-row">
                    <span>Items subtotal</span>
                    <span>&#2547;{{ number_format($subtotal, 0) }}</span>
                </div>
                @if($shippingCost > 0)
                <div class="order-summary-row">
                    <span>Shipping charge</span>
                    <span>&#2547;{{ number_format($shippingCost, 0) }}</span>
                </div>
                @endif
                @if($discount > 0)
                <div class="order-summary-row" style="color:#166534">
                    <span>Discount @if($order->coupon_code)({{ $order->coupon_code }})@endif</span>
                    <span>-&#2547;{{ number_format($discount, 0) }}</span>
                </div>
                @endif
                <div class="order-summary-row total">
                    <span>Grand total</span>
                    <span>&#2547;{{ number_format((float)$order->total_amount, 0) }}</span>
                </div>
            </div>
        </section>

        {{-- Status History --}}
        <section class="card">
            <h2 style="margin:0 0 1rem;font-size:1.15rem">Status history</h2>
            @forelse($order->statusHistories as $h)
                <div style="border-left:3px solid var(--brand);padding:0 0 1rem 1rem;margin-left:.25rem">
                    <strong>{{ $h->from_status ? \Illuminate\Support\Str::headline($h->from_status) : 'Created' }} → {{ \Illuminate\Support\Str::headline($h->to_status) }}</strong>
                    <div class="subtle" style="font-size:.82rem;margin-top:.15rem">
                        {{ $h->created_at?->format('d M Y, g:i A') }} @if($h->changedBy) · {{ $h->changedBy->name }} @endif
                    </div>
                    @if($h->note)
                        <p style="margin:.35rem 0 0;font-size:.88rem;color:#475569;background:#f8fafc;padding:.4rem .6rem;border-radius:.4rem">{{ $h->note }}</p>
                    @endif
                </div>
            @empty
                <p class="subtle">No history available for this order.</p>
            @endforelse
        </section>
    </div>

    <aside class="grid">
        {{-- Customer & Delivery --}}
        <section class="card">
            <h2 style="margin:0 0 1rem;font-size:1.15rem">Customer &amp; delivery</h2>
            <div style="display:grid;gap:.5rem;font-size:.9rem">
                <div>
                    <strong style="font-size:1rem">{{ $order->customer_name }}</strong>
                    @if($order->user)
                        <span class="badge" style="margin-left:.4rem">Registered</span>
                    @else
                        <span class="badge badge-yellow" style="margin-left:.4rem">Guest</span>
                    @endif
                </div>
                <div><span class="subtle">Email:</span> <a href="mailto:{{ $order->email }}" style="color:var(--brand)">{{ $order->email }}</a></div>
                <div><span class="subtle">Phone:</span> <a href="tel:{{ $order->phone }}" style="color:inherit;font-weight:700">{{ $order->phone }}</a></div>
                <hr style="border:0;border-top:1px solid var(--line);margin:.35rem 0">
                <div><span class="subtle">District:</span> <strong>{{ $order->district ?: '—' }}</strong></div>
                <div><span class="subtle">Thana / Area:</span> <strong>{{ $order->thana ?: '—' }}</strong></div>
                <div><span class="subtle">Post office:</span> {{ $order->post_office ?: '—' }} {{ $order->post_code ? '('.$order->post_code.')' : '' }}</div>
                <div style="margin-top:.35rem">
                    <span class="subtle">Full shipping address:</span>
                    <p style="margin:.25rem 0 0;background:#f8fafc;padding:.6rem;border-radius:.5rem;border:1px solid var(--line)">{{ $order->shipping_address }}</p>
                </div>
            </div>
        </section>

        {{-- Payment --}}
        <section class="card">
            <h2 style="margin:0 0 1rem;font-size:1.15rem">Payment</h2>
            <p style="margin:0 0 .75rem;font-size:.9rem">
                <span class="subtle">Method:</span> <strong>{{ $order->payment_method ?: 'Cash on Delivery' }}</strong>
            </p>
            <form method="POST" action="{{ route('admin.orders.updatePayment', $order) }}">
                @csrf
                <div class="field">
                    <label>Payment status</label>
                    <select class="select" name="payment_status">
                        @foreach(['unpaid','pending','paid','partially_paid','failed','refunded','partially_refunded'] as $s)
                            <option value="{{ $s }}" @selected($order->payment_status === $s)>{{ \Illuminate\Support\Str::headline($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field" style="margin-top:.65rem">
                    <label>Transaction ID / Reference</label>
                    <input class="input" name="transaction_id" value="{{ $order->transaction_id }}" placeholder="e.g. TRX12345678">
                </div>
                <button class="btn btn-primary" style="margin-top:1rem;width:100%">Save payment</button>
            </form>
        </section>

        {{-- Fulfillment --}}
        @if(count($allowedNext))
        <section class="card">
            <h2 style="margin:0 0 1rem;font-size:1.15rem">Update fulfillment</h2>
            <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}">
                @csrf
                <div class="field">
                    <label>Next status</label>
                    <select class="select" name="to_status">
                        @foreach($allowedNext as $s)
                            <option value="{{ $s }}">{{ \Illuminate\Support\Str::headline($s) }}</option>
                        @endforeach
                    </select>
                </div>
                @foreach(['shipping_provider'=>'Courier / Shipping provider','tracking_number'=>'Tracking number','tracking_url'=>'Tracking URL','estimated_delivery_at'=>'Estimated delivery'] as $k=>$label)
                    <div class="field" style="margin-top:.65rem">
                        <label>{{ $label }}</label>
                        <input class="input" name="{{ $k }}" value="{{ $order->$k }}" @if($k==='estimated_delivery_at') type="date" @endif placeholder="{{ $k==='tracking_url'?'https://...':'' }}">
                    </div>
                @endforeach
                <div class="field" style="margin-top:.65rem">
                    <label>Internal note</label>
                    <textarea class="textarea" name="note" placeholder="Add an internal progress note..."></textarea>
                </div>
                <button class="btn btn-primary" style="margin-top:1rem;width:100%">Update status</button>
            </form>
        </section>
        @endif

        {{-- Tracking Info if available --}}
        @if($order->tracking_number || $order->tracking_url)
        <section class="card">
            <h2 style="margin:0 0 .75rem;font-size:1.15rem">Tracking Info</h2>
            <p style="margin:0 0 .5rem"><strong>{{ $order->shipping_provider ?: 'Courier' }}:</strong> {{ $order->tracking_number }}</p>
            @if($order->tracking_url)
                <a href="{{ $order->tracking_url }}" target="_blank" rel="noopener" class="btn btn-soft btn-sm" style="display:inline-flex">Track shipment ↗</a>
            @endif
        </section>
        @endif
    </aside>
</div>
@endsection
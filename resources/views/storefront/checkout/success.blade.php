@extends('layouts.app')
@section('title','Order Confirmed')
@section('content')
<main class="container section-gap">
    <div style="max-width:720px;margin:auto;text-align:center;border:1px solid var(--brand-100);border-radius:24px;padding:3rem;background:var(--brand-50)">
        <div style="font-size:3rem" aria-hidden="true">&#10003;</div>
        <h1>Thank you&mdash;your order is confirmed</h1>
        <p>Your order number is <strong>{{ $order->order_number }}</strong>.</p>
        <p>We&rsquo;ll use <strong>{{ $order->email }}</strong> for order updates.</p>
        <div style="background:#fff;border-radius:14px;padding:1.25rem;margin:1.5rem 0;text-align:left;border:1px solid var(--brand-100)">
            <h2 style="margin:0 0 1rem;font-size:1.1rem;color:var(--brand-900)">Items in this order</h2>
            @foreach($order->items as $item)
                <div style="display:flex;align-items:center;justify-content:space-between;gap:1rem;margin-bottom:.85rem;padding-bottom:.85rem;border-bottom:1px dashed #f0e4eb">
                    <div style="display:flex;align-items:center;gap:.75rem">
                        <img src="{{ $item->resolved_image }}" onerror="this.onerror=null;this.src='{{ app(\App\Services\ProductImageResolver::class)->placeholder() }}'" alt="{{ $item->product_name }}" style="width:52px;height:52px;min-width:52px;object-fit:contain;border-radius:8px;border:1px solid var(--brand-100);background:#fff">
                        <div>
                            <strong style="display:block;font-size:.95rem;color:var(--brand-900)">{{ $item->product_name ?: 'Product' }}</strong>
                            <small class="subtle" style="font-size:.8rem">
                                Qty: <strong>{{ $item->quantity }}</strong>
                                @if($item->display_color) · Color: {{ $item->display_color }} @endif
                                @if($item->product_sku) · SKU: {{ $item->product_sku }} @endif
                            </small>
                        </div>
                    </div>
                    <strong style="white-space:nowrap">&#2547;{{ number_format((float)($item->line_total ?: $item->price * $item->quantity), 0) }}</strong>
                </div>
            @endforeach
            <div style="display:flex;justify-content:space-between;padding-top:.5rem;font-size:1.1rem">
                <strong>Total Amount</strong>
                <strong style="color:var(--brand-700)">&#2547;{{ number_format((float)$order->total_amount,0) }}</strong>
            </div>
        </div>
        <a class="nav-btn nav-btn-fill" href="{{ route('products.index') }}">Continue shopping</a>
    </div>
</main>
@endsection
@push('scripts')
<script>
if (typeof window.fbq === 'function') {
    window.fbq('track', 'Purchase', {
        content_ids: @json($order->items->map(fn($item) => (string)($item->product_sku ?: ($item->product?->sku ?: $item->product_id)))->values()->all()),
        content_type: 'product',
        value: {{ (float) $order->total_amount }},
        currency: 'BDT',
        num_items: {{ $order->items->sum('quantity') }}
    }, {
        eventID: @json('order_' . $order->order_number)
    });
}
</script>
@endpush

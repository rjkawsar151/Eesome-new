@extends('layouts.app')
@section('title','Order Confirmed')
@section('content')
<main class="container section-gap">
    <div style="max-width:720px;margin:auto;text-align:center;border:1px solid var(--brand-100);border-radius:24px;padding:3rem;background:var(--brand-50)">
        <div style="font-size:3rem" aria-hidden="true">&#10003;</div>
        <h1>Thank you&mdash;your order is confirmed</h1>
        <p>Your order number is <strong>{{ $order->order_number }}</strong>.</p>
        <p>We&rsquo;ll use <strong>{{ $order->email }}</strong> for order updates.</p>
        <div style="background:#fff;border-radius:14px;padding:1rem;margin:1.5rem 0;text-align:left">
            @foreach($order->items as $item)
                <div style="display:flex;justify-content:space-between;gap:1rem;margin-bottom:.75rem">
                    <span>
                        {{ $item->product_name ?: 'Product' }} &times; {{ $item->quantity }}
                        @if($item->display_color)<br><small>Color: {{ $item->display_color }}</small>@endif
                        @if($item->product_sku)<br><small>SKU: {{ $item->product_sku }}</small>@endif
                    </span>
                    <strong>&#2547;{{ number_format((float)$item->line_total,0) }}</strong>
                </div>
            @endforeach
            <hr>
            <p style="display:flex;justify-content:space-between"><strong>Total</strong><strong>&#2547;{{ number_format((float)$order->total_amount,0) }}</strong></p>
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

@extends('layouts.app')
@section('title','Your Cart')
@push('styles')
<style>
.cart-shell{max-width:1180px;margin:auto}.cart-heading{margin:0 0 1.5rem;font-family:Georgia,serif;font-size:clamp(2rem,4vw,3rem)}.cart-layout{display:grid;grid-template-columns:minmax(0,1.65fr) minmax(300px,.75fr);gap:1.5rem;align-items:start}.cart-items,.summary{border:1px solid #f0e4eb;border-radius:22px;background:#fff;box-shadow:0 10px 35px rgba(65,35,53,.07)}.cart-items{padding:0 1.25rem}.cart-row{display:grid;grid-template-columns:110px minmax(0,1fr) auto;gap:1rem;padding:1.25rem 0;border-bottom:1px solid #f1e7ed}.cart-row:last-child{border:0}.cart-image{width:110px;height:120px;object-fit:contain;border-radius:15px;background:var(--brand-50)}.cart-name{color:inherit;font-size:1.05rem;font-weight:800;text-decoration:none}.variant-meta{display:flex;gap:.65rem;align-items:center;flex-wrap:wrap;margin:.45rem 0;color:var(--text-muted);font-size:.83rem}.color-swatch{width:16px;height:16px;border:1px solid rgba(0,0,0,.2);border-radius:50%}.unit-price{margin:.4rem 0;color:var(--brand-700);font-weight:700}.qty-actions{display:flex;gap:.5rem;align-items:center;flex-wrap:wrap}.qty-actions form{display:flex;gap:.4rem}.qty-actions input{width:68px;min-height:42px;padding:.45rem;border:1px solid #ddd;border-radius:9px}.cart-action{min-height:42px;padding:.55rem .8rem;border:0;border-radius:9px;font-weight:700;cursor:pointer}.cart-update{background:var(--brand-50);color:var(--brand-700)}.cart-remove{background:#fff1f2;color:#b91c1c}.line-total{white-space:nowrap;font-size:1.05rem}.summary{position:sticky;top:88px;padding:1.4rem;background:linear-gradient(145deg,#fff,var(--brand-50))}.summary h2{margin-top:0}.summary-line{display:flex;justify-content:space-between;gap:1rem;padding:.75rem 0;border-bottom:1px solid #eadde5}.summary-note{color:var(--text-muted);font-size:.85rem;line-height:1.55}.checkout-btn,.continue-link{display:flex;min-height:48px;align-items:center;justify-content:center;border-radius:12px;text-decoration:none;font-weight:800}.checkout-btn{margin-top:1rem;background:var(--brand-600);color:#fff}.continue-link{margin-top:.6rem;color:var(--brand-700)}.empty-cart{display:grid;max-width:620px;min-height:360px;margin:auto;place-items:center;padding:3rem;text-align:center;border:1px dashed var(--brand-400);border-radius:24px;background:var(--brand-50)}@media(max-width:760px){.cart-layout{grid-template-columns:1fr}.cart-items{padding:0 .9rem}.cart-row{grid-template-columns:82px minmax(0,1fr);gap:.8rem}.cart-image{width:82px;height:92px}.line-total{grid-column:2}.summary{position:static}.qty-actions{align-items:stretch}.qty-actions form{max-width:100%}}@media(max-width:390px){.cart-row{grid-template-columns:68px minmax(0,1fr)}.cart-image{width:68px;height:78px}.qty-actions input{width:56px}.cart-action{padding:.5rem .6rem}}
</style>
@endpush
@section('content')
<main class="container section-gap"><div class="cart-shell"><h1 class="cart-heading">Your cart</h1>
@php
    $subtotal = 0;
@endphp
@if(count($items))
<div class="cart-layout"><section class="cart-items" aria-label="Cart items">
@foreach($items as $item)
@php
$product=$isDb?$item->product:$item['product'];
$variant=$isDb?$item->productVariant:$item['variant'];
$qty=$isDb?$item->quantity:$item['quantity'];
$key=$isDb?(string)$item->id:$item['key'];
$unit=(float)($variant?->effective_price??$product->effective_price);
$line=$unit*$qty;$subtotal+=$line;
$img=$variant?->image??$product->images->first()?->image_path??$product->image;
@endphp
<article class="cart-row">
<img class="cart-image" src="{{ app(\App\Services\ProductImageResolver::class)->resolve($img) }}" alt="{{ $product->name }}">
<div><a class="cart-name" href="{{ route('products.show',$product->slug) }}">{{ $product->name }}</a>
@if($variant)<div class="variant-meta"><span class="color-swatch" style="background:{{ $variant->color_code }}"></span><span>Color: <strong>{{ $variant->color_name?:$variant->color }}</strong></span><span>SKU: {{ $variant->sku }}</span></div>@endif
<p class="unit-price">&#2547;{{ number_format($unit,0) }} each</p>
<div class="qty-actions"><form method="POST" action="{{ route('cart.update',$key) }}">@csrf @method('PATCH')<input type="number" name="quantity" min="1" max="{{ ($product->available_for_preorder || ($variant && $variant->stock <= 0)) ? 100 : max(1, $variant?->stock ?? $product->stock) }}" value="{{ $qty }}" aria-label="Quantity for {{ $product->name }}"><button class="cart-action cart-update">Update</button></form><form method="POST" action="{{ route('cart.destroy',$key) }}" onsubmit="return confirm('Remove this item from your cart?')">@csrf @method('DELETE')<button class="cart-action cart-remove">Remove</button></form></div></div>
<strong class="line-total">&#2547;{{ number_format($line,0) }}</strong>
</article>
@endforeach
</section><aside class="summary"><h2>Order summary</h2><div class="summary-line"><span>Subtotal</span><strong>&#2547;{{ number_format($subtotal,0) }}</strong></div><div class="summary-line"><span>Shipping</span><span>Calculated at checkout</span></div><div class="summary-line"><span>Discount or coupon</span><span>Applied at checkout</span></div><p class="summary-note">Prices, stock, discounts, and delivery charges are securely revalidated before the order is placed.</p><a class="checkout-btn" href="{{ route('checkout.show') }}">Proceed to checkout</a><a class="continue-link" href="{{ route('products.index') }}">Continue shopping</a></aside></div>
@else<div class="empty-cart"><div><h2>Your cart is ready for something beautiful</h2><p>Explore the collection and choose a piece made for your everyday style.</p><a class="checkout-btn" href="{{ route('products.index') }}">Explore products</a></div></div>@endif
</div></main>
@endsection

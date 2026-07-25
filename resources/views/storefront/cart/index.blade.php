@extends('layouts.app')
@section('title','Your Cart')
@push('styles')
<style>.cart-layout{display:grid;grid-template-columns:2fr 1fr;gap:2rem}.cart-row{display:grid;grid-template-columns:90px 1fr auto;gap:1rem;padding:1rem 0;border-bottom:1px solid #eee}.cart-row img{width:90px;height:100px;object-fit:cover;border-radius:12px}.qty{display:flex;gap:.5rem;align-items:center}.qty input{width:70px;padding:.55rem;border:1px solid #ddd;border-radius:8px}.summary{background:var(--brand-50);padding:1.5rem;border-radius:18px;height:max-content}.checkout-btn{display:block;text-align:center;background:var(--brand-600);color:#fff;padding:.9rem;border-radius:10px;text-decoration:none;font-weight:800}@media(max-width:700px){.cart-layout{grid-template-columns:1fr}.cart-row{grid-template-columns:72px 1fr}.cart-row img{width:72px;height:82px}}</style>
@endpush
@section('content')
<main class="container section-gap"><h1>Your cart</h1>
@php $subtotal = 0; @endphp
@if(count($items))
<div class="cart-layout"><section>
@foreach($items as $item)
@php
 $product=$isDb ? $item->product : $item['product']; $qty=$isDb ? $item->quantity : $item['quantity'];
 $line=(float)$product->effective_price*$qty; $subtotal+=$line; $img=$product->images->first()?->image_path ?? $product->image;
@endphp
<article class="cart-row"><img src="{{ app(\App\Services\ProductImageResolver::class)->resolve($img) }}" alt="{{ $product->name }}"><div><a href="{{ route('products.show',$product->slug) }}" style="font-weight:800;color:inherit">{{ $product->name }}</a><p style="color:var(--brand-700)">৳{{ number_format((float)$product->effective_price,0) }} each</p><div class="qty"><form method="POST" action="{{ route('cart.update',$product->id) }}">@csrf @method('PATCH')<input type="number" name="quantity" min="0" value="{{ $qty }}"><button class="nav-btn nav-btn-ghost">Update</button></form><form method="POST" action="{{ route('cart.destroy',$product->id) }}">@csrf @method('DELETE')<button class="nav-btn" style="border:0;color:#b91c1c;background:#fee2e2">Remove</button></form></div></div><strong>৳{{ number_format($line,0) }}</strong></article>
@endforeach</section><aside class="summary"><h2>Order summary</h2><p style="display:flex;justify-content:space-between"><span>Subtotal</span><strong>৳{{ number_format($subtotal,0) }}</strong></p><p style="color:var(--text-muted)">Shipping and discounts are calculated securely at checkout.</p><a class="checkout-btn" href="{{ route('checkout.show') }}">Proceed to checkout</a></aside></div>
@else<div style="text-align:center;padding:5rem 1rem"><h2>Your cart is empty</h2><p>There are plenty of beautiful bags waiting for you.</p><a class="nav-btn nav-btn-fill" href="{{ route('products.index') }}">Continue shopping</a></div>@endif
</main>
@endsection

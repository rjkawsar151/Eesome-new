@extends('layouts.app')
@section('title','Checkout')
@push('styles')
<style>.checkout{display:grid;grid-template-columns:1.35fr .65fr;gap:2rem}.panel{border:1px solid #eee;border-radius:18px;padding:1.5rem}.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem}.field{display:grid;gap:.35rem}.field.full{grid-column:1/-1}.field label{font-weight:700}.field input,.field textarea,.field select{padding:.75rem;border:1px solid #d1d5db;border-radius:9px;width:100%}.field textarea{min-height:100px}.place{width:100%;padding:1rem;border:0;border-radius:10px;background:var(--brand-600);color:#fff;font-weight:800;font-size:1rem}.line{display:flex;justify-content:space-between;gap:1rem;padding:.6rem 0;border-bottom:1px solid #eee}@media(max-width:720px){.checkout{grid-template-columns:1fr}.form-grid{grid-template-columns:1fr}.field.full{grid-column:auto}}</style>
@endpush
@section('content')
<main class="container section-gap"><h1>Secure checkout</h1>
<form method="POST" action="{{ route('checkout.store') }}" class="checkout">@csrf
<section class="panel"><h2>Delivery details</h2><div class="form-grid">
<div class="field"><label for="name">Full name</label><input id="name" name="name" value="{{ old('name',auth()->user()?->name) }}" required></div>
<div class="field"><label for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email',auth()->user()?->email) }}" required></div>
<div class="field"><label for="phone">Phone</label><input id="phone" name="phone" value="{{ old('phone',auth()->user()?->phone) }}" required></div>
<div class="field"><label for="payment_method">Payment</label><select id="payment_method" name="payment_method" required><option value="COD" @selected(old('payment_method')==='COD')>Cash on delivery</option><option value="bKash" @selected(old('payment_method')==='bKash')>bKash</option></select></div>
<div class="field full"><label for="address">Shipping address</label><textarea id="address" name="address" required>{{ old('address') }}</textarea></div>
<div class="field full"><label for="coupon_code">Coupon code (optional)</label><input id="coupon_code" name="coupon_code" value="{{ old('coupon_code') }}"></div>
</div></section>
<aside class="panel">@php($subtotal=0)<h2>Your order</h2>@foreach($items as $item)@php($p=is_array($item)?$item['product']:$item->product;$q=is_array($item)?$item['quantity']:$item->quantity;$line=(float)$p->effective_price*$q;$subtotal+=$line) <div class="line"><span>{{ $p->name }} × {{ $q }}</span><strong>৳{{ number_format($line,0) }}</strong></div>@endforeach<div class="line"><strong>Subtotal</strong><strong>৳{{ number_format($subtotal,0) }}</strong></div><p style="font-size:.85rem;color:var(--text-muted)">The final total, coupon discount, and delivery charge are recalculated on the server.</p><button class="place" type="submit">Place order</button></aside>
</form></main>
@endsection

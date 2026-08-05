@extends('layouts.app')
@section('title', 'Checkout')
@push('styles')
<style>.checkout{display:grid;grid-template-columns:1.35fr .65fr;gap:2rem}.panel{border:1px solid #eee;border-radius:18px;padding:1.5rem}.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem}.field{display:grid;gap:.35rem}.field.full{grid-column:1/-1}.field label{font-weight:700}.field input,.field textarea,.field select{padding:.75rem;border:1px solid #d1d5db;border-radius:9px;width:100%}.field textarea{min-height:100px}.payment-help{grid-column:1/-1;padding:1rem;border-radius:10px;background:#f7f4ef;border:1px solid #e7ded2;line-height:1.6}.payment-help[hidden]{display:none}.place{width:100%;padding:1rem;border:0;border-radius:10px;background:var(--brand-600);color:#fff;font-weight:800;font-size:1rem}.line{display:flex;justify-content:space-between;gap:1rem;padding:.6rem 0;border-bottom:1px solid #eee}.line.total{border-bottom:0;padding-top:.8rem;font-weight:800;font-size:1.05rem;color:var(--text-primary)}.delivery-note{font-size:.82rem;color:var(--text-muted);margin:.75rem 0 1rem}.free-ship{border-radius:12px;padding:.85rem 1rem;margin:0 0 .75rem;border:1px solid #fbcfe8;background:var(--brand-100);color:#831843;font-size:.88rem;line-height:1.5}.free-ship[hidden]{display:none}.free-ship b{color:#be185d}.fs-progress{height:6px;border-radius:999px;background:#fbcfe8;overflow:hidden;margin-top:.6rem}.fs-bar{height:100%;border-radius:999px;background:linear-gradient(90deg,#db2777,#f472b6);transition:width .3s ease}.free-ship.fs-unlocked{border-color:#a7f3d0;background:#ecfdf5;color:#065f46}.free-ship.fs-unlocked b{color:#047857}@media(max-width:720px){.checkout{grid-template-columns:1fr}.form-grid{grid-template-columns:1fr}.field.full{grid-column:auto}}</style>
@endpush
@section('content')
<main class="container section-gap"><h1>Secure checkout</h1>
@if($errors->any())
<div class="alert alert-error" role="alert" aria-live="assertive"><strong>Please check your checkout details.</strong><ul style="margin:.5rem 0 0;padding-left:1.2rem">@foreach($errors->all() as $message)<li>{{ $message }}</li>@endforeach</ul></div>
@endif<form method="POST" action="{{ route('checkout.store') }}" class="checkout">@csrf
<section class="panel"><h2>Delivery details</h2><div class="form-grid">
<div class="field"><label for="name">Full name</label><input id="name" name="name" placeholder="Fatima" value="{{ old('name',auth()->user()?->name) }}" required></div>
<div class="field"><label for="email">Email</label><input id="email" type="email" name="email" placeholder="mail@gmail.com" value="{{ old('email',auth()->user()?->email) }}" required></div>
<div class="field"><label for="phone">Phone</label><input id="phone" name="phone" value="{{ old('phone',auth()->user()?->phone) }}" required></div>
<div class="field"><label for="shipping_method">Delivery method</label><select id="shipping_method" name="shipping_method" required>@foreach($shippingMethods as $method)<option value="{{ $method->code }}" data-charge="{{ (float)$method->base_charge }}" data-threshold="{{ $method->free_shipping_threshold ?? '' }}" data-free="{{ $method->charge_type === 'free' ? '1' : '0' }}" @selected(old('shipping_method')===$method->code)>{{ $method->name }} — {{ $method->charge_type === 'free' ? 'Free' : '৳'.number_format((float)$method->base_charge,0) }}</option>@endforeach</select></div>
<div class="field"><label for="payment_method">Payment</label><select id="payment_method" name="payment_method" required>@foreach($paymentMethods as $method)<option value="{{ $method->code }}" data-instructions="{{ $method->instructions }}" data-account-name="{{ $method->account_name }}" data-account-number="{{ $method->account_number }}" @selected(old('payment_method')===$method->code)>{{ $method->name }}</option>@endforeach</select></div>
<div id="payment-help" class="payment-help" aria-live="polite" hidden><strong>Payment instructions</strong><div id="payment-help-text"></div></div>
<div class="field"><label for="district">District</label><input id="district" name="district" value="{{ old('district') }}" required></div><div class="field"><label for="thana">Thana</label><input id="thana" name="thana" value="{{ old('thana') }}" required></div>
<div class="field"><label for="post_office">Post office</label><input id="post_office" name="post_office" value="{{ old('post_office') }}" required></div><div class="field"><label for="post_code">Post code</label><input id="post_code" name="post_code" inputmode="numeric" value="{{ old('post_code') }}" required></div>
<div class="field full"><label for="address">Shipping address</label><textarea id="address" name="address" required>{{ old('address') }}</textarea></div><div class="field full"><label for="coupon_code">Coupon code (optional)</label><input id="coupon_code" name="coupon_code" value="{{ old('coupon_code') }}"></div></div></section>
<aside class="panel">@php
$currency = app(\App\Services\SiteSettingsRepository::class)->get('currency_symbol', '৳');
$subtotal = 0;
@endphp<h2>Your order</h2>@foreach($items as $item) @php $product=is_array($item)?$item['product']:$item->product; $variant=is_array($item)?$item['variant']:$item->productVariant; $quantity=is_array($item)?$item['quantity']:$item->quantity; $unitPrice=(float)($variant?->effective_price??$product->effective_price); $lineTotal=$unitPrice*$quantity; $subtotal+=$lineTotal; @endphp <div class="line"><span>{{ $product->name }} × {{ $quantity }}</span><strong>{{ $currency }}{{ number_format($lineTotal,0) }}</strong></div>@endforeach
@php
$selectedMethod = $shippingMethods->firstWhere('code', old('shipping_method')) ?? $shippingMethods->first();
$threshold = $selectedMethod?->free_shipping_threshold !== null ? (float)$selectedMethod->free_shipping_threshold : null;
$deliveryFree = $selectedMethod?->charge_type === 'free' || ($threshold !== null && $subtotal >= $threshold);
$delivery = $deliveryFree ? 0.0 : (float)($selectedMethod?->base_charge ?? 0);
$total = $subtotal + $delivery;
$deliveryDisplay = $deliveryFree ? 'Free' : $currency.number_format($delivery,0);
$shipHidden = $threshold === null ? 'hidden' : '';
@endphp
<div class="line"><strong>Subtotal</strong><strong>{{ $currency }}{{ number_format($subtotal,0) }}</strong></div>
<div class="line"><span>Delivery ({{ $selectedMethod?->name }})</span><strong id="delivery-amount">{{ $deliveryDisplay }}</strong></div>
<div class="line total"><span>Total</span><strong id="total-amount">{{ $currency }}{{ number_format($total,0) }}</strong></div>
<div id="free-ship" class="free-ship {{ $deliveryFree ? 'fs-unlocked' : '' }}" data-subtotal="{{ $subtotal }}" data-currency="{{ $currency }}" {{ $shipHidden }}>
@if($threshold !== null)
    @if($deliveryFree)<b>You've unlocked FREE delivery!</b> Enjoy free shipping on this order.@else<b>Add {{ $currency }}{{ number_format($threshold - $subtotal,0) }} more</b> to get FREE delivery!<div class="fs-progress"><div class="fs-bar" style="width:{{ min(100, (int) round($subtotal / max($threshold, 1) * 100)) }}%"></div></div>@endif
@endif
</div>
<p class="delivery-note">The final total, coupon discount, and delivery charge are recalculated on the server.</p><button class="place" type="submit">Place order</button></aside></form></main>
@endsection
@push('scripts')
<script>document.addEventListener('DOMContentLoaded',()=>{const s=document.getElementById('shipping_method'),b=document.getElementById('payment-help'),t=document.getElementById('payment-help-text'),dAmt=document.getElementById('delivery-amount'),tAmt=document.getElementById('total-amount'),fs=document.getElementById('free-ship');const fmt=n=>fs.dataset.currency+Math.round(n).toLocaleString('en-US');const opts=Array.from(s.options).map(o=>({charge:parseFloat(o.dataset.charge||'0'),threshold:o.dataset.threshold?parseFloat(o.dataset.threshold):null,free:o.dataset.free==='1'}));const subtotal=parseFloat(fs.dataset.subtotal||'0');const render=()=>{const m=opts[s.selectedIndex];const unlocked=m.free||(m.threshold!==null&&subtotal>=m.threshold);const delivery=unlocked?0:m.charge;dAmt.textContent=unlocked?'Free':fmt(delivery);tAmt.textContent=fmt(subtotal+delivery);if(m.threshold===null){fs.hidden=true;fs.innerHTML='';return}fs.hidden=false;if(unlocked){fs.className='free-ship fs-unlocked';fs.innerHTML='<b>You\'ve unlocked FREE delivery!</b> Enjoy free shipping on this order.'}else{const pct=Math.min(100,Math.round(subtotal/m.threshold*100));fs.className='free-ship';fs.innerHTML='<b>Add '+fmt(m.threshold-subtotal)+' more</b> to get FREE delivery!<div class="fs-progress"><div class="fs-bar" style="width:'+pct+'%"></div></div>'}};const up=()=>{const o=s.options[s.selectedIndex],p=[];if(o.dataset.instructions)p.push(o.dataset.instructions);if(o.dataset.accountName)p.push('Account name: '+o.dataset.accountName);if(o.dataset.accountNumber)p.push('Account number: '+o.dataset.accountNumber);t.textContent='';p.forEach((v,i)=>{t.append(document.createTextNode(v));if(i<p.length-1)t.append(document.createElement('br'))});b.hidden=p.length===0};s.addEventListener('change',render);document.getElementById('payment_method').addEventListener('change',up);up();render()});</script>
@endpush

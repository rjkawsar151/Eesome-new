@extends('layouts.app')
@section('title', 'Checkout')
@push('styles')
<style>#trx-field[hidden]{display:none !important;}.checkout{display:grid;grid-template-columns:1.35fr .65fr;gap:2rem}.panel{border:1px solid #eee;border-radius:18px;padding:1.5rem}.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem}.field{display:grid;gap:.35rem}.field.full{grid-column:1/-1}.field label{font-weight:700}.field input,.field textarea,.field select{padding:.75rem;border:1px solid #d1d5db;border-radius:9px;width:100%}.field select:disabled{background:#f3f4f6;cursor:not-allowed}.field textarea{min-height:100px}.payment-help{grid-column:1/-1;border-radius:16px;padding:1.25rem;background:linear-gradient(135deg,#fff1f5,#fdf2f8 55%,#fbe8f0);border:1px solid #f9c2d6;box-shadow:0 6px 20px rgba(190,24,93,.06)}.payment-help[hidden]{display:none}.ph-head{display:flex;align-items:center;gap:.8rem;margin-bottom:.9rem}.ph-icon{width:42px;height:42px;flex-shrink:0;display:grid;place-items:center;border-radius:12px;background:var(--brand-600,#be185d);color:#fff}.ph-icon svg{width:22px;height:22px}.ph-title{display:block;font-size:1rem;color:#831843;line-height:1.2}.ph-sub{display:block;font-size:.78rem;color:#9d6b7d;text-transform:uppercase;letter-spacing:.08em}.ph-steps{list-style:none;margin:.25rem 0 0;padding:0;display:grid;gap:.6rem}.ph-steps li{display:flex;align-items:flex-start;gap:.6rem;font-size:.9rem;line-height:1.55;color:#4a2b36}.ph-step-num{width:24px;height:24px;flex-shrink:0;display:grid;place-items:center;border-radius:50%;background:#be185d;color:#fff;font-size:.75rem;font-weight:800;margin-top:.1rem}.ph-account{margin-top:.9rem;padding:.9rem 1rem;border-radius:12px;background:#fff;border:1px solid #f3d5e0;display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap}.ph-account-label{display:block;font-size:.7rem;text-transform:uppercase;letter-spacing:.08em;color:#9d6b7d;font-weight:700}.ph-account-name{display:block;font-size:.9rem;font-weight:700;color:#4a2b36}.ph-account-num{display:block;font-size:1.05rem;font-weight:800;color:#be185d;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;letter-spacing:.04em}.ph-copy{border:0;border-radius:99px;padding:.5rem .9rem;background:#be185d;color:#fff;font-weight:700;cursor:pointer;font-size:.82rem}.ph-copy.copied{background:#047857}.ph-note{margin:.9rem 0 0;padding:.7rem .9rem;border-radius:10px;background:#fff7ed;border:1px solid #fed7aa;color:#9a3412;font-size:.82rem;line-height:1.55;display:flex;gap:.45rem;align-items:flex-start}.ph-note svg{width:15px;height:15px;flex-shrink:0;margin-top:.15rem}.place{width:100%;padding:1rem;border:0;border-radius:10px;background:var(--brand-600);color:#fff;font-weight:800;font-size:1rem;cursor:pointer}.place:hover{background:var(--brand-700,#9d174d)}.line{display:flex;justify-content:space-between;gap:1rem;padding:.6rem 0;border-bottom:1px solid #eee}.line.total{border-bottom:0;padding-top:.8rem;font-weight:800;font-size:1.05rem;color:var(--text-primary)}.delivery-note{font-size:.82rem;color:var(--text-muted);margin:.75rem 0 1rem}.free-ship{border-radius:12px;padding:.85rem 1rem;margin:0 0 .75rem;border:1px solid #fbcfe8;background:var(--brand-100);color:#831843;font-size:.88rem;line-height:1.5}.free-ship[hidden]{display:none}.free-ship b{color:#be185d}.fs-progress{height:6px;border-radius:999px;background:#fbcfe8;overflow:hidden;margin-top:.6rem}.fs-bar{height:100%;border-radius:999px;background:linear-gradient(90deg,#db2777,#f472b6);transition:width .3s ease}.free-ship.fs-unlocked{border-color:#a7f3d0;background:#ecfdf5;color:#065f46}.free-ship.fs-unlocked b{color:#047857}@media(max-width:720px){.checkout{grid-template-columns:1fr}.form-grid{grid-template-columns:1fr}.field.full{grid-column:auto}}</style>
@endpush
@section('content')
<main class="container section-gap"><h1>Secure checkout</h1>
@if($errors->any())
<div class="alert alert-error" role="alert" aria-live="assertive"><strong>Please check your checkout details.</strong><ul style="margin:.5rem 0 0;padding-left:1.2rem">@foreach($errors->all() as $message)<li>{{ $message }}</li>@endforeach</ul></div>
@endif
<form method="POST" action="{{ route('checkout.store') }}" class="checkout">@csrf
<section class="panel"><h2>Delivery details</h2>
<div class="form-grid">
<div class="field"><label for="name">Full name</label><input id="name" name="name" placeholder="Fatima" value="{{ old('name',auth()->user()?->name) }}" required></div>
<div class="field"><label for="email">Email</label><input id="email" type="email" name="email" placeholder="mail@gmail.com" value="{{ old('email',auth()->user()?->email) }}" required></div>
<div class="field"><label for="phone">Phone</label><input id="phone" name="phone" value="{{ old('phone',auth()->user()?->phone) }}" required></div>

<div class="field"><label for="division">Division</label>
<select id="division" name="division" required>
    <option value="">Select Division</option>
    @foreach($divisions as $div)
        <option value="{{ $div->name }}" data-id="{{ $div->id }}" @selected(old('division') === $div->name)>{{ $div->name }}</option>
    @endforeach
</select>
</div>

<div class="field"><label for="district">District</label>
<select id="district" name="district" required disabled>
    <option value="">Select Division first</option>
</select>
</div>

<div class="field"><label for="thana">Thana</label><input id="thana" name="thana" value="{{ old('thana') }}" required></div>
<div class="field"><label for="post_office">Post office</label><input id="post_office" name="post_office" value="{{ old('post_office') }}" required></div>
<div class="field"><label for="post_code">Post code</label><input id="post_code" name="post_code" inputmode="numeric" value="{{ old('post_code') }}" required></div>

<div class="field full"><label for="address">Shipping address</label><textarea id="address" name="address" required>{{ old('address') }}</textarea></div>

<div class="field" style="display:none"><label for="shipping_method">Delivery method</label><select id="shipping_method" name="shipping_method" required>@foreach($shippingMethods as $method)<option value="{{ $method->code }}" @selected(old('shipping_method')===$method->code)>{{ $method->name }}</option>@endforeach</select></div>

<div class="field full"><label for="payment_method">Payment method</label><select id="payment_method" name="payment_method" required>@foreach($paymentMethods as $method)<option value="{{ $method->code }}" data-instructions="{{ $method->instructions }}" data-account-name="{{ $method->account_name }}" data-account-number="{{ $method->account_number }}" data-requires-tx="{{ $method->requires_transaction_id ? '1' : '0' }}" @selected(old('payment_method')===$method->code)>{{ $method->name }}</option>@endforeach</select></div>

<div id="trx-field" class="field full" style="margin-top:.5rem" hidden>
    <label for="transaction_id">Transaction ID / TrxID <span style="color:#be185d">*</span></label>
    <input id="transaction_id" name="transaction_id" placeholder="Enter Transaction ID (e.g. 8N7A6D5C)" value="{{ old('transaction_id') }}">
    <span style="font-size:0.8rem;color:#71717a;margin-top:.2rem">Please enter the Transaction ID / TrxID from your bKash/Nagad/Rocket payment SMS or app.</span>
</div>

<div id="payment-help" class="payment-help" aria-live="polite" hidden><div class="ph-head"><div class="ph-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg></div><div><span class="ph-title" id="payment-help-title">Payment instructions</span><span class="ph-sub" id="payment-help-method"></span></div></div><ol class="ph-steps" id="payment-help-steps"></ol><div class="ph-account" id="payment-help-account" hidden><div class="ph-account-info"><span class="ph-account-label">Send payment to</span><span class="ph-account-name" id="payment-help-acc-name"></span><span class="ph-account-num" id="payment-help-acc-num"></span></div><button type="button" class="ph-copy" id="payment-help-copy">Copy</button></div><p class="ph-note" id="payment-help-note"></p></div>

<div class="field full"><label for="coupon_code">Coupon code (optional)</label><input id="coupon_code" name="coupon_code" value="{{ old('coupon_code') }}"></div></div></section>

<aside class="panel">@php
$currency = app(\App\Services\SiteSettingsRepository::class)->get('currency_symbol', '৳');
$subtotal = 0;
@endphp<h2>Your order</h2>@foreach($items as $item) @php $product=is_array($item)?$item['product']:$item->product; $variant=is_array($item)?$item['variant']:$item->productVariant; $quantity=is_array($item)?$item['quantity']:$item->quantity; $unitPrice=(float)($variant?->effective_price??$product->effective_price); $lineTotal=$unitPrice*$quantity; $subtotal+=$lineTotal; @endphp <div class="line"><span>{{ $product->name }} × {{ $quantity }}</span><strong>{{ $currency }}{{ number_format($lineTotal,0) }}</strong></div>@endforeach

<div class="line"><strong>Subtotal</strong><strong>{{ $currency }}{{ number_format($subtotal,0) }}</strong></div>
<div class="line"><span>Delivery</span><strong id="delivery-amount">৳130</strong></div>
<div class="line total"><span>Total</span><strong id="total-amount">{{ $currency }}{{ number_format($subtotal + 130, 0) }}</strong></div>

<div id="free-ship" class="free-ship" data-subtotal="{{ $subtotal }}" data-currency="{{ $currency }}" data-free-enabled="{{ $deliverySetting->free_delivery_enabled ? '1' : '0' }}" data-free-threshold="{{ (float)$deliverySetting->free_delivery_threshold }}">
</div>
<p class="delivery-note">The final total, coupon discount, and delivery charge are recalculated securely on the server.</p><button class="place" type="submit">Place order</button></aside></form></main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const divSelect = document.getElementById('division');
    const distSelect = document.getElementById('district');
    const dAmt = document.getElementById('delivery-amount');
    const tAmt = document.getElementById('total-amount');
    const fs = document.getElementById('free-ship');

    const subtotal = parseFloat(fs.dataset.subtotal || '0');
    const currency = fs.dataset.currency || '৳';
    const freeEnabled = fs.dataset.freeEnabled === '1';
    const freeThreshold = parseFloat(fs.dataset.freeThreshold || '2000');
    const oldDistrictName = "{{ old('district') }}";

    const fmt = n => currency + Math.round(n).toLocaleString('en-US');

    const renderDelivery = () => {
        let districtCharge = 130; // default initial fallback
        if (distSelect.selectedIndex >= 0 && distSelect.options[distSelect.selectedIndex]) {
            const opt = distSelect.options[distSelect.selectedIndex];
            if (opt.dataset.charge) {
                districtCharge = parseFloat(opt.dataset.charge);
            }
        }

        const unlocked = freeEnabled && (subtotal >= freeThreshold);
        const deliveryCharge = unlocked ? 0 : districtCharge;

        dAmt.textContent = unlocked ? 'Free' : fmt(deliveryCharge);
        tAmt.textContent = fmt(subtotal + deliveryCharge);

        if (!freeEnabled) {
            fs.hidden = true;
            fs.innerHTML = '';
            return;
        }

        fs.hidden = false;
        if (unlocked) {
            fs.className = 'free-ship fs-unlocked';
            fs.innerHTML = '<b>You\'ve unlocked FREE delivery!</b> Enjoy free shipping on this order.';
        } else {
            const needed = freeThreshold - subtotal;
            const pct = Math.min(100, Math.round((subtotal / freeThreshold) * 100));
            fs.className = 'free-ship';
            fs.innerHTML = '<b>Add ' + fmt(needed) + ' more</b> to get FREE delivery!<div class="fs-progress"><div class="fs-bar" style="width:' + pct + '%"></div></div>';
        }
    };

    const loadDistricts = (divisionId, targetDistrictName = '') => {
        if (!divisionId) {
            distSelect.innerHTML = '<option value="">Select Division first</option>';
            distSelect.disabled = true;
            renderDelivery();
            return;
        }

        distSelect.disabled = true;
        distSelect.innerHTML = '<option value="">Loading districts...</option>';

        fetch("{{ route('checkout.districts') }}?division_id=" + encodeURIComponent(divisionId))
            .then(res => res.json())
            .then(districts => {
                distSelect.innerHTML = '<option value="">Select District</option>';
                districts.forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d.name;
                    opt.textContent = d.name;
                    opt.dataset.charge = d.delivery_charge;
                    if (targetDistrictName && d.name === targetDistrictName) {
                        opt.selected = true;
                    }
                    distSelect.appendChild(opt);
                });
                distSelect.disabled = false;
                renderDelivery();
            })
            .catch(err => {
                console.error(err);
                distSelect.innerHTML = '<option value="">Error loading districts</option>';
                renderDelivery();
            });
    };

    divSelect.addEventListener('change', () => {
        const selectedOpt = divSelect.options[divSelect.selectedIndex];
        const divId = selectedOpt ? selectedOpt.dataset.id : '';
        loadDistricts(divId);
    });

    distSelect.addEventListener('change', renderDelivery);

    // Initial load check if old division was selected
    if (divSelect.selectedIndex > 0) {
        const selectedOpt = divSelect.options[divSelect.selectedIndex];
        const divId = selectedOpt ? selectedOpt.dataset.id : '';
        loadDistricts(divId, oldDistrictName);
    } else {
        renderDelivery();
    }

    // Payment Method Help Instructions
    const pSel = document.getElementById('payment_method');
    const b = document.getElementById('payment-help');
    const phTitle = document.getElementById('payment-help-title');
    const phMethod = document.getElementById('payment-help-method');
    const phSteps = document.getElementById('payment-help-steps');
    const phAccount = document.getElementById('payment-help-account');
    const phAccName = document.getElementById('payment-help-acc-name');
    const phAccNum = document.getElementById('payment-help-acc-num');
    const phCopy = document.getElementById('payment-help-copy');
    const phNote = document.getElementById('payment-help-note');

    const step = (n, txt) => {
        const li = document.createElement('li'), num = document.createElement('span'), text = document.createElement('span');
        num.className = 'ph-step-num'; num.textContent = n;
        text.className = 'ph-step-text'; text.innerHTML = txt;
        li.append(num, text);
        phSteps.appendChild(li);
    };

    const trxFld = document.getElementById('trx-field');
    const trxInput = document.getElementById('transaction_id');

    const up = () => {
        const o = pSel.options[pSel.selectedIndex], code = o ? o.value : '';
        const requiresTx = o ? o.dataset.requiresTx === '1' : false;

        if (trxFld && trxInput) {
            if (requiresTx) {
                trxFld.style.display = 'grid';
                trxFld.removeAttribute('hidden');
                trxInput.required = true;
            } else {
                trxFld.style.display = 'none';
                trxFld.setAttribute('hidden', 'hidden');
                trxInput.required = false;
                trxInput.value = '';
            }
        }

        b.hidden = code === 'COD';
        if (b.hidden) return;
        const name = o.textContent.trim(), total = tAmt.textContent.trim();
        phTitle.textContent = 'How to pay by ' + name;
        phMethod.textContent = code;
        phSteps.innerHTML = '';
        let n = 1;
        if (o.dataset.accountNumber) {
            phAccount.hidden = false;
            phAccName.textContent = o.dataset.accountName || '—';
            phAccNum.textContent = o.dataset.accountNumber;
            step(n++, 'Send <b>' + total + '</b> using <b>' + name + '</b> to the account number below.');
        } else {
            phAccount.hidden = true;
            step(n++, 'Make your payment of <b>' + total + '</b> using <b>' + name + '</b>.');
        }
        step(n++, 'After paying, note down the <b>Transaction ID</b> shown in your payment app.');
        if (requiresTx) {
            step(n++, 'Enter the <b>Transaction ID</b> in the box above to confirm your order.');
        } else {
            step(n++, 'Keep the <b>Transaction ID</b> safe — you may be asked to share it for verification.');
        }
        phNote.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg><span>' + ((o.dataset.instructions || 'Your order is confirmed only after your payment is verified. Do not send money before your order total is confirmed.')) + '</span>';
    };

    phCopy.addEventListener('click', () => {
        if (!navigator.clipboard) return;
        navigator.clipboard.writeText(phAccNum.textContent).then(() => {
            phCopy.textContent = 'Copied';
            phCopy.classList.add('copied');
            setTimeout(() => { phCopy.textContent = 'Copy'; phCopy.classList.remove('copied'); }, 1800);
        });
    });

    pSel.addEventListener('change', up);
    up();
});
</script>
@endpush

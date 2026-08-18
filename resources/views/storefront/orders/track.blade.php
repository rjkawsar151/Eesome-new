@extends('layouts.app')
@section('title', $order ? 'Tracking Order #' . $order->order_number : 'Track Your Order')
@section('meta_description', 'Track your EESOME order status in real time.')

@push('styles')
<style>
    .track-wrapper {
        padding: 3rem 0 5rem;
        background: linear-gradient(180deg, #FCECF4 0%, #FFFFFF 250px);
        min-height: 70vh;
    }
    .track-card {
        background: #ffffff;
        border: 1px solid #F2DFE8;
        border-radius: 16px;
        padding: 2.2rem;
        box-shadow: 0 10px 30px rgba(126, 53, 88, 0.06);
        margin-bottom: 2rem;
    }
    .track-header {
        text-align: center;
        max-width: 620px;
        margin: 0 auto 2.5rem;
    }
    .track-eyebrow {
        display: inline-block;
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #B54A7B;
        margin-bottom: 0.5rem;
    }
    .track-title {
        font-family: Georgia, 'Times New Roman', serif;
        font-size: clamp(1.8rem, 3.5vw, 2.6rem);
        font-weight: 500;
        color: #6F2F50;
        margin: 0 0 0.6rem;
    }
    .track-sub {
        color: #6E5260;
        font-size: 0.98rem;
        line-height: 1.6;
        margin: 0;
    }
    
    .track-form {
        display: grid;
        grid-template-columns: minmax(0, 1.5fr) minmax(0, 1.5fr) auto;
        gap: 0.85rem;
        max-width: 720px;
        margin: 1.8rem auto 0;
    }
    .track-input {
        width: 100%;
        padding: 0.85rem 1.1rem;
        border: 1.5px solid #F2D2E0;
        border-radius: 10px;
        font: inherit;
        font-size: 0.95rem;
        background: #FFF9FC;
        color: #3E2A35;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .track-input:focus {
        outline: none;
        border-color: #B54A7B;
        background: #FFFFFF;
        box-shadow: 0 0 0 4px rgba(181, 74, 123, 0.12);
    }
    .track-btn {
        background: #B54A7B;
        color: #ffffff;
        border: 0;
        border-radius: 10px;
        padding: 0.85rem 1.8rem;
        font: inherit;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        white-space: nowrap;
        transition: background 0.2s, transform 0.1s;
    }
    .track-btn:hover { background: #9E3A68; }
    .track-btn:active { transform: scale(0.98); }

    /* Timeline presentation */
    .timeline-wrap {
        margin: 2rem 0;
        padding: 1.5rem 1rem;
        background: #FFF6FA;
        border: 1px solid #F2D2E0;
        border-radius: 14px;
    }
    .timeline-steps {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        position: relative;
        margin: 1rem 0;
    }
    .timeline-steps::before {
        content: '';
        position: absolute;
        top: 18px;
        left: 30px;
        right: 30px;
        height: 3px;
        background: #F2DFE8;
        z-index: 1;
    }
    .timeline-progress-line {
        position: absolute;
        top: 18px;
        left: 30px;
        height: 3px;
        background: #B54A7B;
        z-index: 2;
        transition: width 0.4s ease;
    }
    .timeline-step {
        position: relative;
        z-index: 3;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        flex: 1;
    }
    .timeline-node {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #FFFFFF;
        border: 3px solid #F2DFE8;
        color: #8A6C7B;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.85rem;
        transition: all 0.3s;
    }
    .timeline-step.completed .timeline-node {
        background: #B54A7B;
        border-color: #B54A7B;
        color: #FFFFFF;
    }
    .timeline-step.active .timeline-node {
        background: #FFFFFF;
        border-color: #B54A7B;
        color: #B54A7B;
        box-shadow: 0 0 0 6px rgba(181, 74, 123, 0.18);
    }
    .timeline-label {
        margin-top: 0.75rem;
        font-size: 0.85rem;
        font-weight: 700;
        color: #6E5260;
    }
    .timeline-step.completed .timeline-label,
    .timeline-step.active .timeline-label {
        color: #6F2F50;
    }

    /* Cancelled Alert Box */
    .alert-cancelled-box {
        background: #FFF1F1;
        border: 1.5px solid #E9A6A6;
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        display: flex;
        gap: 1rem;
        align-items: flex-start;
        margin: 1.5rem 0;
    }
    .alert-cancelled-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #D92D20;
        color: #ffffff;
        font-size: 1.4rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .alert-cancelled-content {
        color: #842029;
        font-size: 0.95rem;
        line-height: 1.55;
    }

    /* Hold / Refund Box */
    .alert-notice-box {
        background: #FFFBEB;
        border: 1.5px solid #FCD34D;
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        color: #92400E;
        margin: 1.5rem 0;
    }

    /* Order details layout */
    .order-grid {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(0, 1fr);
        gap: 1.5rem;
        margin-top: 1.5rem;
    }
    .order-table {
        width: 100%;
        border-collapse: collapse;
    }
    .order-table th, .order-table td {
        padding: 0.85rem 0.5rem;
        border-bottom: 1px solid #F2DFE8;
        text-align: left;
    }
    .order-table th {
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #B54A7B;
    }

    @media (max-width: 768px) {
        .track-form { grid-template-columns: 1fr; }
        .order-grid { grid-template-columns: 1fr; }
        .timeline-steps { flex-direction: column; align-items: flex-start; gap: 1.25rem; }
        .timeline-steps::before { top: 20px; bottom: 20px; left: 18px; width: 3px; height: auto; }
        .timeline-progress-line { display: none; }
        .timeline-step { flex-direction: row; gap: 1rem; text-align: left; }
        .timeline-label { margin-top: 0; }
    }
</style>
@endpush

@section('content')
<div class="track-wrapper">
    <div class="container">
        
        <div class="track-header">
            <span class="track-eyebrow">Real-Time Tracking</span>
            <h1 class="track-title">Track Your Order</h1>
            <p class="track-sub">Enter your order number below to check the latest status of your order.</p>
            
            <form class="track-form" method="POST" action="{{ route('orders.track.search') }}">
                @csrf
                <input class="track-input" type="text" name="order_number" value="{{ old('order_number', $searchedOrderNumber ?? ($order->order_number ?? '')) }}" placeholder="Order Number (e.g. #EES-1001)" required>
                <input class="track-input" type="text" name="email_or_phone" value="{{ old('email_or_phone', $searchedEmailOrPhone ?? '') }}" placeholder="Email or Phone (optional for verification)">
                <button class="track-btn" type="submit">Track Order</button>
            </form>
        </div>

        @if(!empty($error))
            <div class="track-card" style="border-color:#FCA5A5; background:#FFF1F1; text-align:center;">
                <p style="color:#991B1B; font-weight:700; font-size:1.05rem; margin:0 0 0.5rem;">{{ $error }}</p>
                <p style="color:#7F1D1D; font-size:0.9rem; margin:0;">
                    Need assistance? Call <a href="tel:{{ preg_replace('/\D/', '', $supportPhone) }}" style="color:#991B1B; font-weight:700;">{{ $supportPhone }}</a> or email <a href="mailto:{{ $supportEmail }}" style="color:#991B1B; font-weight:700;">{{ $supportEmail }}</a>.
                </p>
            </div>
        @endif

        @if($order)
            @php
                $statusKey = strtolower($order->order_status);
                
                // Status stage calculation
                $stage = match($statusKey) {
                    'awaiting', 'pending', 'confirmed', 'waiting_for_confirmation' => 1,
                    'processing' => 2,
                    'shipped', 'in_transit' => 3,
                    'out_for_delivery' => 4,
                    'delivered', 'completed' => 5,
                    default => 1,
                };
                
                $stagePercent = match($stage) {
                    1 => 0,
                    2 => 25,
                    3 => 50,
                    4 => 75,
                    5 => 100,
                };

                $badgeBg = match ($statusKey) {
                    'confirmed', 'delivered' => '#DCFCE7',
                    'processing' => '#FFF6FA',
                    'shipped', 'in_transit' => '#F3E8FF',
                    'cancelled', 'refunded' => '#FFF1F1',
                    'on_hold', 'pending', 'awaiting' => '#FEF3C7',
                    default => '#FFF6FA',
                };
                $badgeText = match ($statusKey) {
                    'confirmed', 'delivered' => '#15803D',
                    'processing' => '#B54A7B',
                    'shipped', 'in_transit' => '#6B21A8',
                    'cancelled', 'refunded' => '#D92D20',
                    'on_hold', 'pending', 'awaiting' => '#92400E',
                    default => '#6F2F50',
                };
            @endphp

            <div class="track-card">
                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; padding-bottom:1.25rem; border-bottom:1px solid #F2DFE8;">
                    <div>
                        <h2 style="font-family:Georgia, serif; font-size:1.5rem; color:#6F2F50; margin:0 0 0.2rem;">
                            Order #{{ $order->order_number }}
                        </h2>
                        <span style="font-size:0.88rem; color:#6E5260;">
                            Placed {{ $order->created_at ? $order->created_at->format('M d, Y · g:i A') : 'Recently' }}
                        </span>
                    </div>
                    <span style="display:inline-block; padding:8px 18px; border-radius:999px; background:{{ $badgeBg }}; color:{{ $badgeText }}; font-weight:800; font-size:0.85rem; letter-spacing:0.04em; text-transform:uppercase;">
                        {{ \Illuminate\Support\Str::headline($order->order_status) }}
                    </span>
                </div>

                {{-- Status Timeline or Special Condition Notice --}}
                @if($statusKey === 'cancelled')
                    <div class="alert-cancelled-box">
                        <div class="alert-cancelled-icon">!</div>
                        <div class="alert-cancelled-content">
                            <strong style="display:block; font-size:1.05rem; margin-bottom:0.3rem;">We're sorry — your order has been cancelled.</strong>
                            If you have any questions or would like help placing a new order, please call us at
                            <a href="tel:{{ preg_replace('/\D/', '', $supportPhone) }}" style="color:#842029; font-weight:700;">{{ $supportPhone }}</a>
                            or email us at
                            <a href="mailto:{{ $supportEmail }}" style="color:#842029; font-weight:700;">{{ $supportEmail }}</a>.
                        </div>
                    </div>
                @elseif($statusKey === 'on_hold')
                    <div class="alert-notice-box">
                        <strong style="display:block; font-size:1.05rem; margin-bottom:0.3rem;">Your order is currently on hold</strong>
                        We will update you as soon as processing resumes. If you need assistance, please contact support.
                    </div>
                @elseif(in_array($statusKey, ['refunded', 'partially_refunded']))
                    <div class="alert-notice-box" style="background:#FFF1F1; border-color:#FCA5A5; color:#842029;">
                        <strong style="display:block; font-size:1.05rem; margin-bottom:0.3rem;">Refund Processed</strong>
                        A refund has been issued for this order. Please allow 3-5 business days for it to reflect in your account.
                    </div>
                @else
                    <div class="timeline-wrap">
                        <div class="timeline-steps">
                            <div class="timeline-progress-line" style="width: {{ $stagePercent }}%;"></div>
                            
                            <div class="timeline-step {{ $stage >= 1 ? ($stage > 1 ? 'completed' : 'active') : '' }}">
                                <div class="timeline-node">{{ $stage > 1 ? '✓' : '1' }}</div>
                                <span class="timeline-label">Order Confirmed</span>
                            </div>
                            <div class="timeline-step {{ $stage >= 2 ? ($stage > 2 ? 'completed' : 'active') : '' }}">
                                <div class="timeline-node">{{ $stage > 2 ? '✓' : '2' }}</div>
                                <span class="timeline-label">Processing</span>
                            </div>
                            <div class="timeline-step {{ $stage >= 3 ? ($stage > 3 ? 'completed' : 'active') : '' }}">
                                <div class="timeline-node">{{ $stage > 3 ? '✓' : '3' }}</div>
                                <span class="timeline-label">Shipped</span>
                            </div>
                            <div class="timeline-step {{ $stage >= 4 ? ($stage > 4 ? 'completed' : 'active') : '' }}">
                                <div class="timeline-node">{{ $stage > 4 ? '✓' : '4' }}</div>
                                <span class="timeline-label">Out for Delivery</span>
                            </div>
                            <div class="timeline-step {{ $stage >= 5 ? 'completed' : '' }}">
                                <div class="timeline-node">{{ $stage >= 5 ? '✓' : '5' }}</div>
                                <span class="timeline-label">Delivered</span>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Shipment Tracking Card if Carrier/Tracking exists --}}
                @if($order->tracking_number || $order->tracking_url || $order->shipping_provider)
                <div style="background:#FFF9FC; border:1px solid #F2DFE8; border-radius:12px; padding:1.25rem 1.5rem; margin-bottom:1.5rem;">
                    <h3 style="font-size:0.95rem; font-weight:800; text-transform:uppercase; letter-spacing:0.06em; color:#B54A7B; margin:0 0 0.75rem;">
                        Shipment & Tracking Information
                    </h3>
                    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem;">
                        <div>
                            @if($order->shipping_provider)
                                <div style="font-size:0.9rem; color:#5A4450;"><strong>Carrier:</strong> {{ $order->shipping_provider }}</div>
                            @endif
                            @if($order->tracking_number)
                                <div style="font-size:0.9rem; color:#5A4450; margin-top:0.25rem;"><strong>Tracking Number:</strong> {{ $order->tracking_number }}</div>
                            @endif
                            @if($order->estimated_delivery_at)
                                <div style="font-size:0.88rem; color:#8A6C7B; margin-top:0.25rem;">
                                    Estimated Delivery: {{ \Carbon\Carbon::parse($order->estimated_delivery_at)->format('M d, Y') }}
                                </div>
                            @endif
                        </div>
                        @if($order->tracking_url)
                            <a href="{{ $order->tracking_url }}" target="_blank" rel="noopener noreferrer" class="track-btn" style="background:#6F2F50; text-decoration:none; padding:0.65rem 1.25rem; font-size:0.88rem;">
                                Track Shipment ↗
                            </a>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Order Details Grid --}}
                <div class="order-grid">
                    <div>
                        <h3 style="font-size:1.1rem; font-family:Georgia, serif; color:#6F2F50; margin:0 0 0.85rem;">Items Summary</h3>
                        <table class="order-table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th style="text-align:center;">Qty</th>
                                    <th style="text-align:right;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    @php
                                        $img = null;
                                        if ($item->variant && $item->variant->image_path) {
                                            $img = asset('storage/' . $item->variant->image_path);
                                        } elseif ($item->product && $item->product->images->isNotEmpty()) {
                                            $img = asset('storage/' . $item->product->images->first()->image_path);
                                        } elseif ($item->product && $item->product->image) {
                                            $img = app(\App\Services\ProductImageResolver::class)->resolve($item->product->image);
                                        } else {
                                            $img = app(\App\Services\ProductImageResolver::class)->placeholder();
                                        }
                                        $lineVal = (float)($item->line_total ?: ($item->price * $item->quantity));
                                    @endphp
                                    <tr>
                                        <td>
                                            <div style="display:flex; gap:0.75rem; align-items:center;">
                                                <img src="{{ $img }}" alt="{{ $item->product_name }}" style="width:52px; height:52px; object-fit:cover; border-radius:8px; border:1px solid #F2DFE8;">
                                                <div>
                                                    <strong style="color:#3E2A35; font-size:0.92rem; display:block;">{{ $item->product_name }}</strong>
                                                    @if($item->display_color)<span style="font-size:0.8rem; color:#8A6C7B;">Color: {{ $item->display_color }}</span>@endif
                                                </div>
                                            </div>
                                        </td>
                                        <td style="text-align:center; font-weight:700; color:#5A4450;">{{ $item->quantity }}</td>
                                        <td style="text-align:right; font-weight:700; color:#B54A7B; white-space:nowrap;">৳{{ number_format($lineVal, 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div>
                        <h3 style="font-size:1.1rem; font-family:Georgia, serif; color:#6F2F50; margin:0 0 0.85rem;">Order Summary</h3>
                        <div style="background:#FFF9FC; border:1px solid #F2DFE8; border-radius:12px; padding:1.2rem; font-size:0.92rem; color:#5A4450;">
                            @if(!empty($order->subtotal_amount) && (float)$order->subtotal_amount > 0)
                                <div style="display:flex; justify-content:space-between; margin-bottom:0.5rem;">
                                    <span>Subtotal</span>
                                    <span>৳{{ number_format((float)$order->subtotal_amount, 0) }}</span>
                                </div>
                            @endif
                            @if(!empty($order->discount_amount) && (float)$order->discount_amount > 0)
                                <div style="display:flex; justify-content:space-between; margin-bottom:0.5rem; color:#D92D20;">
                                    <span>Discount</span>
                                    <span>-৳{{ number_format((float)$order->discount_amount, 0) }}</span>
                                </div>
                            @endif
                            @if(!empty($order->shipping_charge))
                                <div style="display:flex; justify-content:space-between; margin-bottom:0.5rem;">
                                    <span>Shipping</span>
                                    <span>৳{{ number_format((float)$order->shipping_charge, 0) }}</span>
                                </div>
                            @endif
                            <div style="display:flex; justify-content:space-between; margin-top:0.75rem; padding-top:0.75rem; border-top:1.5px solid #F2DFE8; font-weight:800; font-size:1.05rem; color:#3E2A35;">
                                <span>Total</span>
                                <span style="color:#B54A7B;">৳{{ number_format((float)$order->total_amount, 0) }}</span>
                            </div>

                            @if($order->shipping_address)
                                <div style="margin-top:1.2rem; padding-top:1rem; border-top:1px dashed #F2DFE8;">
                                    <div style="font-size:0.78rem; font-weight:800; text-transform:uppercase; letter-spacing:0.08em; color:#B54A7B; margin-bottom:0.4rem;">Shipping Destination</div>
                                    <div style="font-size:0.88rem; color:#5A4450; line-height:1.5;">
                                        <strong>{{ $order->customer_name }}</strong><br>
                                        {!! nl2br(e($order->shipping_address)) !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Status History Timeline section if available --}}
                @if($order->statusHistories->isNotEmpty())
                    <div style="margin-top:2rem; padding-top:1.5rem; border-top:1px solid #F2DFE8;">
                        <h3 style="font-size:1.05rem; font-family:Georgia, serif; color:#6F2F50; margin:0 0 1rem;">Status History Log</h3>
                        <div style="display:grid; gap:0.75rem;">
                            @foreach($order->statusHistories as $history)
                                <div style="display:flex; justify-content:space-between; align-items:center; background:#FFF6FA; padding:0.75rem 1rem; border-radius:10px; border:1px solid #F2D2E0; font-size:0.88rem;">
                                    <div>
                                        <strong style="color:#6F2F50;">{{ \Illuminate\Support\Str::headline($history->to_status) }}</strong>
                                        @if($history->note)
                                            <span style="color:#8A6C7B; margin-left:0.5rem;">— {{ $history->note }}</span>
                                        @endif
                                    </div>
                                    <span style="font-size:0.8rem; color:#8A6C7B;">{{ $history->created_at ? $history->created_at->format('M d, Y · g:i A') : '' }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif

    </div>
</div>
@endsection

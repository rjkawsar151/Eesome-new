<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $isNew ? 'New Order' : 'Order Update' }} - {{ $order->order_number }}</title>
</head>
<body style="margin:0;padding:0;background:#fdf2f8;font-family:'Segoe UI',Arial,Helvetica,sans-serif;font-size:15px;color:#4a1d30;">
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#fdf2f8;padding:28px 0;">
  <tr><td align="center">
    <table width="640" cellpadding="0" cellspacing="0" border="0" style="max-width:640px;width:100%;">

      {{-- Header --}}
      <tr>
        <td style="background:linear-gradient(135deg,#831843 0%,#ec4899 100%);border-radius:14px 14px 0 0;padding:28px 36px 22px;text-align:center;">
          <p style="margin:0 0 4px;font-family:Georgia,'Times New Roman',serif;font-size:22px;font-weight:700;color:#fff;">{{ config('app.name', 'EESOME') }} - Admin Alert</p>
          <p style="margin:0;font-size:12px;color:rgba(255,255,255,.8);text-transform:uppercase;letter-spacing:.06em;">
            {{ $isNew ? 'New Order Received' : 'Order Status Updated' }}
          </p>
        </td>
      </tr>

      {{-- Event Badge + Order Number --}}
      <tr>
        <td style="background:#fff;padding:22px 36px 0;">
          <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
              <td>
                <span style="display:inline-block;padding:6px 16px;border-radius:999px;font-size:12px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;background:{{ $isNew ? '#ec4899' : '#be185d' }};color:#fff;">
                  {{ $isNew ? 'New Order' : $statusLabel }}
                </span>
              </td>
              <td style="text-align:right;">
                <span style="font-size:18px;font-weight:800;color:#831843;">#{{ $order->order_number }}</span>
              </td>
            </tr>
          </table>
        </td>
      </tr>

      {{-- Customer Details --}}
      <tr>
        <td style="background:#fff;padding:20px 36px 0;">
          <p style="margin:0 0 10px;font-size:12px;color:#a3547a;letter-spacing:.05em;text-transform:uppercase;font-weight:600;">Customer Details</p>
          <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#fdf2f8;border-radius:10px;overflow:hidden;border:1px solid #fbcfe8;">
            <tr><td style="padding:10px 14px;font-size:13px;border-bottom:1px solid #fce7f3;color:#a3547a;width:110px;font-weight:600;">Name</td><td style="padding:10px 14px;font-size:14px;border-bottom:1px solid #fce7f3;color:#4a1d30;font-weight:700;">{{ $order->customer_name }}</td></tr>
            <tr><td style="padding:10px 14px;font-size:13px;border-bottom:1px solid #fce7f3;color:#a3547a;font-weight:600;">Phone</td><td style="padding:10px 14px;font-size:14px;border-bottom:1px solid #fce7f3;color:#4a1d30;">{{ $order->phone ?? 'N/A' }}</td></tr>
            <tr><td style="padding:10px 14px;font-size:13px;border-bottom:1px solid #fce7f3;color:#a3547a;font-weight:600;">Email</td><td style="padding:10px 14px;font-size:14px;border-bottom:1px solid #fce7f3;color:#4a1d30;">{{ $order->email ?? 'N/A' }}</td></tr>
            <tr><td style="padding:10px 14px;font-size:13px;color:#a3547a;font-weight:600;vertical-align:top;">Address</td><td style="padding:10px 14px;font-size:14px;color:#4a1d30;line-height:1.5;">{{ $order->shipping_address }}</td></tr>
          </table>
        </td>
      </tr>

      {{-- Order Status & Payment --}}
      <tr>
        <td style="background:#fff;padding:16px 36px 0;">
          <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #fbcfe8;border-radius:10px;overflow:hidden;">
            <tr style="background:#fce7f3;">
              <td style="padding:10px 14px;font-size:13px;color:#a3547a;font-weight:600;border-bottom:1px solid #fbcfe8;">Order Status</td>
              <td style="padding:10px 14px;font-size:13px;color:#a3547a;font-weight:600;border-bottom:1px solid #fbcfe8;">Payment Status</td>
              <td style="padding:10px 14px;font-size:13px;color:#a3547a;font-weight:600;border-bottom:1px solid #fbcfe8;">Payment Method</td>
            </tr>
            <tr>
              <td style="padding:12px 14px;font-size:14px;font-weight:700;color:#be185d;">{{ \Illuminate\Support\Str::headline($order->order_status) }}</td>
              <td style="padding:12px 14px;font-size:14px;font-weight:600;color:{{ $order->payment_status === 'paid' ? '#15803d' : '#b45309' }};">{{ \Illuminate\Support\Str::headline($order->payment_status) }}</td>
              <td style="padding:12px 14px;font-size:14px;color:#6b3a52;">{{ \Illuminate\Support\Str::headline($order->payment_method ?? 'N/A') }}</td>
            </tr>
          </table>
        </td>
      </tr>

      {{-- Order Items --}}
      <tr>
        <td style="background:#fff;padding:16px 36px 0;">
          <p style="margin:0 0 10px;font-size:12px;color:#a3547a;letter-spacing:.05em;text-transform:uppercase;font-weight:600;">Items Ordered</p>
          <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #fbcfe8;border-radius:10px;overflow:hidden;">
            <thead>
              <tr style="background:#fce7f3;">
                <th style="padding:9px 12px;text-align:left;font-size:11px;color:#a3547a;font-weight:600;letter-spacing:.04em;text-transform:uppercase;border-bottom:1px solid #fbcfe8;">Product</th>
                <th style="padding:9px 12px;text-align:center;font-size:11px;color:#a3547a;font-weight:600;letter-spacing:.04em;text-transform:uppercase;border-bottom:1px solid #fbcfe8;">Qty</th>
                <th style="padding:9px 12px;text-align:right;font-size:11px;color:#a3547a;font-weight:600;letter-spacing:.04em;text-transform:uppercase;border-bottom:1px solid #fbcfe8;">Price</th>
                <th style="padding:9px 12px;text-align:right;font-size:11px;color:#a3547a;font-weight:600;letter-spacing:.04em;text-transform:uppercase;border-bottom:1px solid #fbcfe8;">Subtotal</th>
              </tr>
            </thead>
            <tbody>
              @foreach($order->items as $i => $item)
              <tr style="{{ $i % 2 === 0 ? 'background:#fff;' : 'background:#fff5f9;' }}">
                <td style="padding:10px 12px;font-size:13px;color:#4a1d30;border-bottom:1px solid #fce7f3;vertical-align:top;">
                  <strong>{{ $item->product_name }}</strong>
                  @if($item->display_color) <span style="color:#a3547a;"> - {{ $item->display_color }}</span>@endif
                  @if($item->product_sku) <br><span style="font-size:11px;color:#c98bab;">SKU: {{ $item->product_sku }}</span>@endif
                </td>
                <td style="padding:10px 12px;text-align:center;font-size:13px;color:#4a1d30;border-bottom:1px solid #fce7f3;vertical-align:top;">{{ $item->quantity }}</td>
                <td style="padding:10px 12px;text-align:right;font-size:13px;color:#4a1d30;border-bottom:1px solid #fce7f3;vertical-align:top;white-space:nowrap;">&#2547;{{ number_format((float)$item->price, 0) }}</td>
                <td style="padding:10px 12px;text-align:right;font-size:13px;font-weight:600;color:#be185d;border-bottom:1px solid #fce7f3;vertical-align:top;white-space:nowrap;">&#2547;{{ number_format((float)$item->line_total, 0) }}</td>
              </tr>
              @endforeach
            </tbody>
            <tfoot>
              @if(!empty($order->subtotal_amount) && (float)$order->subtotal_amount > 0)
              <tr style="background:#fff5f9;">
                <td colspan="3" style="padding:9px 12px;text-align:right;font-size:12px;color:#a3547a;border-top:1px solid #fbcfe8;">Subtotal</td>
                <td style="padding:9px 12px;text-align:right;font-size:12px;color:#6b3a52;font-weight:600;border-top:1px solid #fbcfe8;">&#2547;{{ number_format((float)$order->subtotal_amount, 0) }}</td>
              </tr>
              @endif
              @if(!empty($order->discount_amount) && (float)$order->discount_amount > 0)
              <tr style="background:#fff5f9;">
                <td colspan="3" style="padding:9px 12px;text-align:right;font-size:12px;color:#a3547a;">Discount</td>
                <td style="padding:9px 12px;text-align:right;font-size:12px;color:#dc2626;font-weight:600;">-&#2547;{{ number_format((float)$order->discount_amount, 0) }}</td>
              </tr>
              @endif
              @if(!empty($order->shipping_charge) && (float)$order->shipping_charge > 0)
              <tr style="background:#fff5f9;">
                <td colspan="3" style="padding:9px 12px;text-align:right;font-size:12px;color:#a3547a;">Shipping</td>
                <td style="padding:9px 12px;text-align:right;font-size:12px;color:#6b3a52;font-weight:600;">&#2547;{{ number_format((float)$order->shipping_charge, 0) }}</td>
              </tr>
              @endif
              <tr style="background:#fce7f3;">
                <td colspan="3" style="padding:11px 12px;text-align:right;font-size:14px;font-weight:700;color:#4a1d30;border-top:2px solid #f9a8d4;">Total</td>
                <td style="padding:11px 12px;text-align:right;font-size:15px;font-weight:800;color:#be185d;border-top:2px solid #f9a8d4;white-space:nowrap;">&#2547;{{ number_format((float)$order->total_amount, 0) }}</td>
              </tr>
            </tfoot>
          </table>
        </td>
      </tr>

      {{-- Tracking (if set) --}}
      @if($order->tracking_number)
      <tr>
        <td style="background:#fff;padding:16px 36px 0;">
          <div style="background:#fdf2f8;border-radius:10px;padding:12px 16px;border-left:3px solid #ec4899;">
            <p style="margin:0;font-size:13px;font-weight:700;color:#be185d;">Tracking: {{ $order->shipping_provider ? $order->shipping_provider.' - ' : '' }}{{ $order->tracking_number }}</p>
          </div>
        </td>
      </tr>
      @endif

      {{-- CTA --}}
      <tr>
        <td style="background:#fff;padding:24px 36px;text-align:center;">
          <a href="{{ $adminUrl }}" target="_blank" style="display:inline-block;padding:12px 30px;background:linear-gradient(135deg,#831843,#ec4899);color:#fff;text-decoration:none;border-radius:999px;font-size:14px;font-weight:700;letter-spacing:.03em;">Open Order in Admin Panel</a>
        </td>
      </tr>

      {{-- Footer --}}
      <tr>
        <td style="background:linear-gradient(135deg,#831843,#ec4899);border-radius:0 0 14px 14px;padding:16px 36px;text-align:center;">
          <p style="margin:0;font-size:11px;color:rgba(255,255,255,.75);letter-spacing:.04em;">{{ config('app.name', 'EESOME') }} Admin System &copy; {{ date('Y') }}</p>
        </td>
      </tr>

    </table>
  </td></tr>
</table>
</body>
</html>

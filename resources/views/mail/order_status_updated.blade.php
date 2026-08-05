<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Order {{ $order->order_number }} - {{ $statusLabel }}</title>
</head>
<body style="margin:0;padding:0;background:#fdf2f8;font-family:'Segoe UI',Arial,Helvetica,sans-serif;font-size:15px;color:#4a1d30;">
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#fdf2f8;padding:30px 0;">
  <tr><td align="center">
    <table width="620" cellpadding="0" cellspacing="0" border="0" style="max-width:620px;width:100%;">

      {{-- Header --}}
      <tr>
        <td style="background:linear-gradient(135deg,#831843 0%,#ec4899 100%);border-radius:16px 16px 0 0;padding:36px 40px 28px;text-align:center;">
          <p style="margin:0 0 6px;font-family:Georgia,'Times New Roman',serif;font-size:28px;font-weight:700;color:#fff;letter-spacing:-0.5px;">{{ config('app.name', 'EESOME') }}</p>
          <p style="margin:0;font-size:13px;color:rgba(255,255,255,.8);letter-spacing:.05em;text-transform:uppercase;">Order Update Notification</p>
        </td>
      </tr>

      {{-- Status Banner --}}
      <tr>
        <td style="background:#fff;padding:28px 40px 0;">
          <p style="margin:0 0 10px;font-size:13px;color:#a3547a;letter-spacing:.05em;text-transform:uppercase;font-weight:600;">Order Status</p>
          <span style="display:inline-block;padding:8px 22px;border-radius:999px;font-size:14px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;background:{{ $statusColor }};color:#fff;">{{ $statusLabel }}</span>
        </td>
      </tr>

      {{-- Greeting --}}
      <tr>
        <td style="background:#fff;padding:24px 40px 0;">
          <p style="margin:0 0 14px;font-size:17px;color:#4a1d30;font-weight:600;">Hello {{ $order->customer_name }},</p>
          @if(!empty($isNew))
          <p style="margin:0 0 8px;color:#6b3a52;line-height:1.6;">Thank you for your order! We have received <strong>#{{ $order->order_number }}</strong> and it is now <strong>{{ $statusLabel }}</strong>.</p>
          <p style="margin:0;color:#6b3a52;line-height:1.6;">We will notify you as soon as your order status changes.</p>
          @else
          <p style="margin:0 0 8px;color:#6b3a52;line-height:1.6;">Your order <strong>#{{ $order->order_number }}</strong> has been updated to <strong>{{ $statusLabel }}</strong>.</p>
          @if($order->order_status === 'shipped' || $order->order_status === 'in_transit')
          <p style="margin:0;color:#6b3a52;line-height:1.6;">Your package is on its way! Track it using the details below.</p>
          @elseif($order->order_status === 'delivered')
          <p style="margin:0;color:#6b3a52;line-height:1.6;">Your order has been delivered. We hope you love it!</p>
          @elseif($order->order_status === 'cancelled')
          <p style="margin:0;color:#6b3a52;line-height:1.6;">Your order has been cancelled. If you have questions, please reach out to us.</p>
          @elseif($order->order_status === 'processing')
          <p style="margin:0;color:#6b3a52;line-height:1.6;">We are preparing your order and will ship it soon!</p>
          @elseif($order->order_status === 'confirmed')
          <p style="margin:0;color:#6b3a52;line-height:1.6;">Your order has been confirmed. We will start preparing it shortly.</p>
          @elseif($order->order_status === 'waiting_for_confirmation')
          <p style="margin:0;color:#6b3a52;line-height:1.6;">Your order is awaiting confirmation. We will notify you once it is confirmed.</p>
          @else
          <p style="margin:0;color:#6b3a52;line-height:1.6;">Thank you for shopping with {{ config('app.name', 'EESOME') }}.</p>
          @endif
          @endif
        </td>
      </tr>

      {{-- Order Items Table --}}
      <tr>
        <td style="background:#fff;padding:24px 40px 0;">
          <p style="margin:0 0 12px;font-size:13px;color:#a3547a;letter-spacing:.05em;text-transform:uppercase;font-weight:600;">Order Items</p>
          <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #fbcfe8;border-radius:10px;overflow:hidden;">
            <thead>
              <tr style="background:#fce7f3;">
                <th style="padding:10px 14px;text-align:left;font-size:12px;color:#a3547a;font-weight:600;letter-spacing:.04em;text-transform:uppercase;border-bottom:1px solid #fbcfe8;">Product</th>
                <th style="padding:10px 14px;text-align:center;font-size:12px;color:#a3547a;font-weight:600;letter-spacing:.04em;text-transform:uppercase;border-bottom:1px solid #fbcfe8;">Qty</th>
                <th style="padding:10px 14px;text-align:right;font-size:12px;color:#a3547a;font-weight:600;letter-spacing:.04em;text-transform:uppercase;border-bottom:1px solid #fbcfe8;">Price</th>
                <th style="padding:10px 14px;text-align:right;font-size:12px;color:#a3547a;font-weight:600;letter-spacing:.04em;text-transform:uppercase;border-bottom:1px solid #fbcfe8;">Subtotal</th>
              </tr>
            </thead>
            <tbody>
              @foreach($order->items as $index => $item)
              <tr style="{{ $index % 2 === 0 ? 'background:#fff;' : 'background:#fff5f9;' }}">
                <td style="padding:12px 14px;border-bottom:1px solid #fce7f3;vertical-align:top;">
                  <p style="margin:0;font-size:14px;font-weight:600;color:#4a1d30;">{{ $item->product_name }}</p>
                  @if($item->display_color)<p style="margin:2px 0 0;font-size:12px;color:#a3547a;">Color: {{ $item->display_color }}</p>@endif
                  @if($item->product_sku)<p style="margin:2px 0 0;font-size:12px;color:#a3547a;">SKU: {{ $item->product_sku }}</p>@endif
                </td>
                <td style="padding:12px 14px;text-align:center;font-size:14px;color:#4a1d30;border-bottom:1px solid #fce7f3;vertical-align:top;">{{ $item->quantity }}</td>
                <td style="padding:12px 14px;text-align:right;font-size:14px;color:#4a1d30;border-bottom:1px solid #fce7f3;vertical-align:top;white-space:nowrap;">&#2547;{{ number_format((float)$item->price, 0) }}</td>
                <td style="padding:12px 14px;text-align:right;font-size:14px;font-weight:600;color:#be185d;border-bottom:1px solid #fce7f3;vertical-align:top;white-space:nowrap;">&#2547;{{ number_format((float)$item->line_total, 0) }}</td>
              </tr>
              @endforeach
            </tbody>
            <tfoot>
              @if(!empty($order->subtotal_amount) && (float)$order->subtotal_amount > 0)
              <tr style="background:#fff5f9;">
                <td colspan="3" style="padding:10px 14px;text-align:right;font-size:13px;color:#a3547a;border-top:1px solid #fbcfe8;">Subtotal</td>
                <td style="padding:10px 14px;text-align:right;font-size:13px;color:#6b3a52;font-weight:600;border-top:1px solid #fbcfe8;">&#2547;{{ number_format((float)$order->subtotal_amount, 0) }}</td>
              </tr>
              @endif
              @if(!empty($order->discount_amount) && (float)$order->discount_amount > 0)
              <tr style="background:#fff5f9;">
                <td colspan="3" style="padding:10px 14px;text-align:right;font-size:13px;color:#a3547a;">Discount</td>
                <td style="padding:10px 14px;text-align:right;font-size:13px;color:#dc2626;font-weight:600;">-&#2547;{{ number_format((float)$order->discount_amount, 0) }}</td>
              </tr>
              @endif
              @if(!empty($order->shipping_charge) && (float)$order->shipping_charge > 0)
              <tr style="background:#fff5f9;">
                <td colspan="3" style="padding:10px 14px;text-align:right;font-size:13px;color:#a3547a;">Shipping</td>
                <td style="padding:10px 14px;text-align:right;font-size:13px;color:#6b3a52;font-weight:600;">&#2547;{{ number_format((float)$order->shipping_charge, 0) }}</td>
              </tr>
              @endif
              <tr style="background:#fce7f3;">
                <td colspan="3" style="padding:12px 14px;text-align:right;font-size:15px;font-weight:700;color:#4a1d30;border-top:2px solid #f9a8d4;">Total</td>
                <td style="padding:12px 14px;text-align:right;font-size:16px;font-weight:800;color:#be185d;border-top:2px solid #f9a8d4;white-space:nowrap;">&#2547;{{ number_format((float)$order->total_amount, 0) }}</td>
              </tr>
            </tfoot>
          </table>
        </td>
      </tr>

      {{-- Shipping Address --}}
      <tr>
        <td style="background:#fff;padding:24px 40px 0;">
          <p style="margin:0 0 8px;font-size:13px;color:#a3547a;letter-spacing:.05em;text-transform:uppercase;font-weight:600;">Shipping Address</p>
          <div style="background:#fdf2f8;border-radius:10px;padding:14px 16px;border-left:3px solid #ec4899;">
            <p style="margin:0;color:#4a1d30;font-size:14px;line-height:1.6;"><strong>{{ $order->customer_name }}</strong><br>{{ $order->shipping_address }}@if($order->phone)<br><span style="color:#a3547a;">Phone: {{ $order->phone }}</span>@endif</p>
          </div>
        </td>
      </tr>

      {{-- Tracking Info --}}
      @if($order->tracking_number)
      <tr>
        <td style="background:#fff;padding:24px 40px 0;">
          <p style="margin:0 0 8px;font-size:13px;color:#a3547a;letter-spacing:.05em;text-transform:uppercase;font-weight:600;">Tracking Info</p>
          <div style="background:#fdf2f8;border-radius:10px;padding:14px 16px;border-left:3px solid #ec4899;">
            <p style="margin:0;color:#be185d;font-size:14px;font-weight:600;">{{ $order->shipping_provider ? $order->shipping_provider.' - ' : '' }}{{ $order->tracking_number }}</p>
            @if($order->estimated_delivery_at)<p style="margin:6px 0 0;font-size:13px;color:#831843;">Estimated Delivery: {{ \Carbon\Carbon::parse($order->estimated_delivery_at)->format('M d, Y') }}</p>@endif
          </div>
        </td>
      </tr>
      @if($order->tracking_url)
      <tr>
        <td style="background:#fff;padding:20px 40px 0;text-align:center;">
          <a href="{{ $order->tracking_url }}" target="_blank" style="display:inline-block;padding:13px 32px;background:linear-gradient(135deg,#831843,#ec4899);color:#fff;text-decoration:none;border-radius:999px;font-size:14px;font-weight:700;letter-spacing:.03em;">Track My Shipment</a>
        </td>
      </tr>
      @endif
      @endif

      {{-- Footer Note --}}
      <tr>
        <td style="background:#fff;padding:28px 40px;">
          <hr style="border:none;border-top:1px solid #fbcfe8;margin:0 0 20px;">
          <p style="margin:0;font-size:13px;color:#b06f90;line-height:1.7;text-align:center;">Questions about your order? Contact us at <a href="mailto:{{ config('mail.from.address') }}" style="color:#be185d;text-decoration:none;">{{ config('mail.from.address') }}</a>.</p>
          <p style="margin:10px 0 0;font-size:13px;color:#c98bab;text-align:center;">Thank you for shopping with {{ config('app.name', 'EESOME') }}!</p>
        </td>
      </tr>

      {{-- Footer --}}
      <tr>
        <td style="background:linear-gradient(135deg,#831843,#ec4899);border-radius:0 0 16px 16px;padding:20px 40px;text-align:center;">
          <p style="margin:0;font-size:12px;color:rgba(255,255,255,.75);letter-spacing:.04em;">&copy; {{ date('Y') }} {{ config('app.name', 'EESOME') }} &mdash; All rights reserved</p>
        </td>
      </tr>

    </table>
  </td></tr>
</table>
</body>
</html>

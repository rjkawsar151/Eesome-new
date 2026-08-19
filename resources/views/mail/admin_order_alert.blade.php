<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $isNew ? 'New Order' : 'Order Update' }} #{{ $order->order_number }}</title>
</head>
<body style="margin:0; padding:0; background:#f4f5f7; font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size:14px; line-height:1.5; color:#1f2937;">
  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f4f5f7; padding:24px 12px;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px; width:100%; background:#ffffff; border-radius:8px; overflow:hidden; border:1px solid #e5e7eb; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
          
          <!-- Simple Header -->
          <tr>
            <td style="padding:20px 24px; border-bottom:1px solid #e5e7eb; background:#ffffff;">
              <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                  <td>
                    <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#6b7280; margin-bottom:4px;">
                      {{ config('app.name', 'EESOME') }} &bull; Admin Notification
                    </div>
                    <div style="font-size:20px; font-weight:700; color:#111827;">
                      {{ $isNew ? '🔔 New Order Placed' : '⚡ Order Status Updated' }}
                    </div>
                  </td>
                  <td align="right" valign="top">
                    <span style="display:inline-block; padding:4px 10px; border-radius:4px; font-size:12px; font-weight:600; text-transform:uppercase; background:{{ $isNew ? '#dbeafe; color:#1e40af;' : '#f3f4f6; color:#374151;' }}">
                      #{{ $order->order_number }}
                    </span>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Quick Summary Cards -->
          <tr>
            <td style="padding:20px 24px; background:#f9fafb; border-bottom:1px solid #e5e7eb;">
              <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                  <td width="33%" style="padding:0 8px 0 0;">
                    <div style="font-size:11px; color:#6b7280; text-transform:uppercase; font-weight:600;">Total Amount</div>
                    <div style="font-size:18px; font-weight:700; color:#111827; margin-top:2px;">৳{{ number_format((float)$order->total_amount, 0) }}</div>
                  </td>
                  <td width="33%" style="padding:0 8px;">
                    <div style="font-size:11px; color:#6b7280; text-transform:uppercase; font-weight:600;">Payment</div>
                    <div style="font-size:13px; font-weight:600; color:{{ $order->payment_status === 'paid' ? '#059669' : '#d97706' }}; margin-top:2px;">
                      {{ ucfirst($order->payment_status) }} ({{ strtoupper($order->payment_method) }})
                    </div>
                  </td>
                  <td width="33%" style="padding:0 0 0 8px;">
                    <div style="font-size:11px; color:#6b7280; text-transform:uppercase; font-weight:600;">Order Status</div>
                    <div style="font-size:13px; font-weight:600; color:#4f46e5; margin-top:2px;">
                      {{ \Illuminate\Support\Str::headline($order->order_status) }}
                    </div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Customer & Delivery Information -->
          <tr>
            <td style="padding:20px 24px; border-bottom:1px solid #e5e7eb;">
              <div style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:#374151; margin-bottom:12px;">
                Customer & Shipping
              </div>
              <table width="100%" cellpadding="0" cellspacing="0" border="0" style="font-size:13px;">
                <tr>
                  <td width="110" style="color:#6b7280; padding-bottom:6px; font-weight:500;">Customer:</td>
                  <td style="color:#111827; padding-bottom:6px; font-weight:600;">{{ $order->customer_name }}</td>
                </tr>
                <tr>
                  <td style="color:#6b7280; padding-bottom:6px; font-weight:500;">Phone:</td>
                  <td style="color:#111827; padding-bottom:6px;">
                    <a href="tel:{{ $order->phone }}" style="color:#2563eb; text-decoration:none; font-weight:600;">{{ $order->phone }}</a>
                  </td>
                </tr>
                @if($order->email)
                <tr>
                  <td style="color:#6b7280; padding-bottom:6px; font-weight:500;">Email:</td>
                  <td style="color:#111827; padding-bottom:6px;">
                    <a href="mailto:{{ $order->email }}" style="color:#2563eb; text-decoration:none;">{{ $order->email }}</a>
                  </td>
                </tr>
                @endif
                <tr>
                  <td valign="top" style="color:#6b7280; padding-bottom:6px; font-weight:500;">Address:</td>
                  <td style="color:#111827; padding-bottom:6px;">{{ $order->shipping_address }}</td>
                </tr>
                @if($order->shipping_method)
                <tr>
                  <td style="color:#6b7280; padding-bottom:6px; font-weight:500;">Delivery:</td>
                  <td style="color:#111827; padding-bottom:6px;">{{ $order->shipping_method }}</td>
                </tr>
                @endif
                @if($order->transaction_id)
                <tr>
                  <td style="color:#6b7280; padding-bottom:6px; font-weight:500;">Trx ID:</td>
                  <td style="color:#059669; padding-bottom:6px; font-weight:700;">{{ $order->transaction_id }}</td>
                </tr>
                @endif
                @if($order->notes)
                <tr>
                  <td valign="top" style="color:#6b7280; font-weight:500;">Notes:</td>
                  <td style="color:#6b7280; font-style:italic;">{{ $order->notes }}</td>
                </tr>
                @endif
              </table>
            </td>
          </tr>

          <!-- Items Ordered Table -->
          <tr>
            <td style="padding:20px 24px 12px; border-bottom:1px solid #e5e7eb;">
              <div style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:#374151; margin-bottom:12px;">
                Items ({{ $order->items->count() }})
              </div>
              <table width="100%" cellpadding="0" cellspacing="0" border="0" style="font-size:13px;">
                <thead>
                  <tr style="border-bottom:1px solid #e5e7eb; color:#6b7280;">
                    <th align="left" style="padding-bottom:8px; font-weight:600; font-size:11px; text-transform:uppercase;">Item</th>
                    <th align="center" width="60" style="padding-bottom:8px; font-weight:600; font-size:11px; text-transform:uppercase;">Qty</th>
                    <th align="right" width="100" style="padding-bottom:8px; font-weight:600; font-size:11px; text-transform:uppercase;">Total</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($order->items as $item)
                  <tr>
                    <td style="padding:8px 0; border-top:1px solid #f3f4f6;">
                      <div style="font-weight:600; color:#111827;">{{ $item->product_name }}</div>
                      @if($item->display_color)
                        <div style="font-size:12px; color:#6b7280;">Color: {{ $item->display_color }}</div>
                      @endif
                      @if($item->product_sku)
                        <div style="font-size:11px; color:#9ca3af;">SKU: {{ $item->product_sku }}</div>
                      @endif
                    </td>
                    <td align="center" style="padding:8px 0; border-top:1px solid #f3f4f6; color:#374151;">
                      {{ $item->quantity }}
                    </td>
                    <td align="right" style="padding:8px 0; border-top:1px solid #f3f4f6; font-weight:600; color:#111827;">
                      ৳{{ number_format((float)($item->line_total ?: ($item->price * $item->quantity)), 0) }}
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </td>
          </tr>

          <!-- Action Button -->
          <tr>
            <td align="center" style="padding:24px;">
              <a href="{{ $adminUrl }}" target="_blank" style="display:inline-block; padding:12px 28px; background:#111827; color:#ffffff; font-size:14px; font-weight:600; text-decoration:none; border-radius:6px; text-align:center;">
                View Order #{{ $order->order_number }} in Admin &rarr;
              </a>
            </td>
          </tr>

          <!-- Simple Footer -->
          <tr>
            <td align="center" style="padding:16px 24px; background:#f9fafb; border-top:1px solid #e5e7eb; font-size:12px; color:#9ca3af;">
              {{ config('app.name', 'EESOME') }} Admin System &bull; {{ now()->format('M d, Y h:i A') }}
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>

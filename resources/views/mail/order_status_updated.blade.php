<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="x-apple-disable-message-reformatting">
  <title>{{ $statusTitle }} — Order #{{ $order->order_number }}</title>
  <style>
    @media only screen and (max-width: 620px) {
      .container { width: 100% !important; }
      .mobile-pad { padding-left: 20px !important; padding-right: 20px !important; }
      .mobile-block { display: block !important; width: 100% !important; }
      .mobile-center { text-align: center !important; }
      .logo { width: 220px !important; max-width: 88% !important; }
      .item-img { width: 72px !important; height: 72px !important; }
      .button { display: block !important; width: auto !important; }
    }
  </style>
</head>
<body style="margin:0; padding:0; background:#FCECF4; font-family:Arial, Helvetica, sans-serif; color:#3E2A35;">

  <!-- Hidden preheader text -->
  <div style="display:none; max-height:0; overflow:hidden; opacity:0; color:transparent; line-height:1px; font-size:1px;">
    Order update for #{{ $order->order_number }}: {{ $statusLabel }}
  </div>

  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%; background:#FCECF4;">
    <tr>
      <td align="center" style="padding:28px 12px;">

        <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" class="container" style="width:600px; max-width:600px; background:#FFFFFF; border-radius:18px; overflow:hidden; box-shadow:0 8px 28px rgba(126,53,88,0.10);">

          <!-- Brand header -->
          <tr>
            <td align="center" style="padding:32px 28px 26px; background:#FFF6FA; border-bottom:1px solid #F4D7E4;">
              <div style="font-family:Georgia, 'Times New Roman', serif; font-size:32px; font-weight:700; letter-spacing:6px; color:#6F2F50; text-transform:uppercase; margin:0; line-height:1;">
                EESOME
              </div>
              <div style="font-size:11px; letter-spacing:2px; text-transform:uppercase; color:#B54A7B; margin-top:6px; font-weight:600;">
                The Charm In You
              </div>
            </td>
          </tr>

          <!-- Status intro -->
          <tr>
            <td class="mobile-pad" style="padding:34px 42px 18px;">
              <div style="font-size:13px; line-height:18px; font-weight:700; letter-spacing:1.2px; text-transform:uppercase; color:#B54A7B; margin-bottom:10px;">
                Order update
              </div>

              <h1 style="margin:0 0 12px; font-family:Georgia, 'Times New Roman', serif; font-size:30px; line-height:38px; font-weight:400; color:#6F2F50;">
                {{ $statusTitle }}
              </h1>

              <p style="margin:0; font-size:16px; line-height:25px; color:#5A4450;">
                Hi {{ $order->customer_name }},<br>
                {{ $statusMessage }}
              </p>
            </td>
          </tr>

          <!-- Status badge / meta -->
          <tr>
            <td class="mobile-pad" style="padding:6px 42px 22px;">
              <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#FFF6FA; border:1px solid #F2D2E0; border-radius:12px;">
                <tr>
                  <td style="padding:16px 18px;">
                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                      <tr>
                        <td class="mobile-block mobile-center" valign="middle" style="font-size:14px; line-height:20px; color:#6E5260;">
                          <strong style="color:#3E2A35;">Order #{{ $order->order_number }}</strong><br>
                          Placed {{ $order->created_at ? $order->created_at->format('M d, Y') : date('M d, Y') }}
                        </td>
                        <td class="mobile-block mobile-center" align="right" valign="middle" style="padding-top:0;">
                          <span style="display:inline-block; margin-top:6px; padding:8px 13px; border-radius:999px; background:{{ $badgeBg }}; color:{{ $badgeText }}; font-size:12px; line-height:16px; font-weight:700; letter-spacing:.4px; text-transform:uppercase;">
                            {{ $statusLabel }}
                          </span>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- CANCELLED ONLY: this section renders ONLY when boolean isCancelled === true -->
          @if(!empty($isCancelled))
          <tr>
            <td class="mobile-pad" style="padding:0 42px 24px;">
              <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#FFF1F1; border:1px solid #E9A6A6; border-radius:12px;">
                <tr>
                  <td width="56" valign="top" style="padding:18px 0 18px 18px;">
                    <div style="width:34px; height:34px; line-height:34px; text-align:center; border-radius:50%; background:#D92D20; color:#FFFFFF; font-size:22px; font-weight:700; font-family:Arial, Helvetica, sans-serif;">!</div>
                  </td>
                  <td valign="top" style="padding:17px 18px 18px 12px; color:#842029;">
                    <div style="font-size:16px; line-height:22px; font-weight:700; margin-bottom:5px;">We're sorry — your order has been cancelled.</div>
                    <div style="font-size:14px; line-height:22px;">
                      If you have any questions or would like help placing a new order, please call us at
                      <a href="tel:{{ $supportPhoneLink }}" style="color:#842029; font-weight:700;">{{ $supportPhone }}</a>
                      or email us at
                      <a href="mailto:{{ $supportEmail }}" style="color:#842029; font-weight:700;">{{ $supportEmail }}</a>.
                    </div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          @endif

          <!-- Tracking / primary action: render when trackingUrl is available -->
          @if(!empty($trackingUrl))
          <tr>
            <td class="mobile-pad" align="center" style="padding:0 42px 28px;">
              <a href="{{ $trackingUrl }}" class="button" style="display:inline-block; background:#B54A7B; color:#FFFFFF; text-decoration:none; font-size:15px; line-height:18px; font-weight:700; padding:14px 24px; border-radius:10px;">
                Track Order
              </a>
              @if($order->tracking_number)
              <div style="margin-top:10px; font-size:12px; line-height:18px; color:#8A6C7B;">
                {{ $order->shipping_provider ? $order->shipping_provider . ' Tracking:' : 'Tracking:' }} {{ $order->tracking_number }}
              </div>
              @endif
            </td>
          </tr>
          @endif

          <!-- Order details header -->
          <tr>
            <td class="mobile-pad" style="padding:0 42px 10px;">
              <h2 style="margin:0 0 14px; font-family:Georgia, 'Times New Roman', serif; font-size:22px; line-height:28px; font-weight:400; color:#6F2F50;">Order details</h2>
            </td>
          </tr>

          <!-- Items list -->
          @foreach($order->items as $item)
          @php
              $itemImage = null;
              if ($item->variant && $item->variant->image_path) {
                  $itemImage = asset('storage/' . $item->variant->image_path);
              } elseif ($item->product && $item->product->images->isNotEmpty()) {
                  $itemImage = asset('storage/' . $item->product->images->first()->image_path);
              } elseif ($item->product && $item->product->image) {
                  $itemImage = app(\App\Services\ProductImageResolver::class)->resolve($item->product->image);
              } else {
                  $itemImage = app(\App\Services\ProductImageResolver::class)->placeholder();
              }
              $lineTotalVal = (float)($item->line_total ?: ($item->price * $item->quantity));
          @endphp
          <tr>
            <td class="mobile-pad" style="padding:0 42px;">
              <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border-top:1px solid #F2DFE8;">
                <tr>
                  <td width="88" valign="top" style="padding:16px 14px 16px 0;">
                    <img src="{{ $itemImage }}" width="78" height="78" alt="{{ $item->product_name }}" class="item-img" style="display:block; width:78px; height:78px; object-fit:cover; border-radius:10px; border:0; background:#FFF6FA;">
                  </td>
                  <td valign="top" style="padding:16px 10px 16px 0; font-size:14px; line-height:21px; color:#5A4450;">
                    <strong style="display:block; color:#3E2A35; font-size:15px;">{{ $item->product_name }}</strong>
                    @if($item->display_color)<span>Color: {{ $item->display_color }}</span><br>@endif
                    @if($item->product_sku)<span style="font-size:12px; color:#8A6C7B;">SKU: {{ $item->product_sku }}</span><br>@endif
                    Qty: {{ $item->quantity }}
                  </td>
                  <td width="100" align="right" valign="top" style="padding:16px 0; font-size:14px; line-height:21px; font-weight:700; color:#3E2A35; white-space:nowrap;">
                    &#2547;{{ number_format($lineTotalVal, 0) }}
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          @endforeach

          <!-- Totals -->
          <tr>
            <td class="mobile-pad" style="padding:8px 42px 30px;">
              <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#FFF9FC; border:1px solid #F2DFE8; border-radius:12px;">
                <tr>
                  <td style="padding:16px 18px;">
                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="font-size:14px; line-height:22px; color:#5A4450;">
                      @if(!empty($order->subtotal_amount) && (float)$order->subtotal_amount > 0)
                      <tr><td>Subtotal</td><td align="right">&#2547;{{ number_format((float)$order->subtotal_amount, 0) }}</td></tr>
                      @endif
                      @if(!empty($order->discount_amount) && (float)$order->discount_amount > 0)
                      <tr><td>Discount</td><td align="right" style="color:#D92D20;">-&#2547;{{ number_format((float)$order->discount_amount, 0) }}</td></tr>
                      @endif
                      @if(!empty($order->shipping_charge))
                      <tr><td>Shipping</td><td align="right">&#2547;{{ number_format((float)$order->shipping_charge, 0) }}</td></tr>
                      @endif
                      <tr>
                        <td style="padding-top:10px; border-top:1px solid #EFD8E3; font-size:16px; font-weight:700; color:#3E2A35;">Total</td>
                        <td align="right" style="padding-top:10px; border-top:1px solid #EFD8E3; font-size:16px; font-weight:700; color:#B54A7B;">&#2547;{{ number_format((float)$order->total_amount, 0) }}</td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Shipping information -->
          @if(!empty($order->shipping_address))
          <tr>
            <td class="mobile-pad" style="padding:0 42px 32px;">
              <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                <tr>
                  <td class="mobile-block" width="50%" valign="top" style="padding:0 10px 12px 0;">
                    <div style="font-size:12px; line-height:17px; font-weight:700; letter-spacing:.8px; text-transform:uppercase; color:#B54A7B; margin-bottom:7px;">Ship to</div>
                    <div style="font-size:14px; line-height:21px; color:#5A4450;">
                      <strong>{{ $order->customer_name }}</strong><br>
                      {!! nl2br(e($order->shipping_address)) !!}
                      @if($order->phone)<br><span style="color:#8A6C7B;">Phone: {{ $order->phone }}</span>@endif
                    </div>
                  </td>
                  <td class="mobile-block" width="50%" valign="top" style="padding:0 0 12px 10px;">
                    <div style="font-size:12px; line-height:17px; font-weight:700; letter-spacing:.8px; text-transform:uppercase; color:#B54A7B; margin-bottom:7px;">Shipping method</div>
                    <div style="font-size:14px; line-height:21px; color:#5A4450;">
                      {{ $order->shipping_method ?: 'Standard Delivery' }}
                      @if($order->shipping_provider)
                      <br><span style="font-size:12px; color:#8A6C7B;">Carrier: {{ $order->shipping_provider }}</span>
                      @endif
                    </div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          @endif

          <!-- Help / footer -->
          <tr>
            <td align="center" class="mobile-pad" style="padding:26px 42px 30px; background:#FFF6FA; border-top:1px solid #F4D7E4;">
              <div style="font-size:15px; line-height:22px; font-weight:700; color:#6F2F50; margin-bottom:7px;">Need help with your order?</div>
              <div style="font-size:13px; line-height:21px; color:#765D69;">
                Call <a href="tel:{{ $supportPhoneLink }}" style="color:#B54A7B; font-weight:700; text-decoration:none;">{{ $supportPhone }}</a>
                &nbsp;•&nbsp;
                Email <a href="mailto:{{ $supportEmail }}" style="color:#B54A7B; font-weight:700; text-decoration:none;">{{ $supportEmail }}</a>
              </div>
              <div style="margin-top:8px; font-size:13px; line-height:20px;">
                <a href="{{ $websiteUrl }}" style="color:#B54A7B; font-weight:700; text-decoration:none;">{{ $websiteDisplay }}</a>
              </div>
              <div style="margin-top:18px; font-size:11px; line-height:17px; color:#9B8390;">
                © {{ date('Y') }} EESOME. The Charm In You.<br>
                This email was sent because there is an update to order #{{ $order->order_number }}.
              </div>
            </td>
          </tr>

        </table>

      </td>
    </tr>
  </table>
</body>
</html>

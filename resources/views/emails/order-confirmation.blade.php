<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Confirmed</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f8;font-family:'Segoe UI',Arial,sans-serif;color:#1a1f2e;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f8;padding:32px 0;">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08);">

      {{-- Header --}}
      <tr>
        <td style="background:#1a1f2e;padding:28px 32px;text-align:center;">
          <div style="font-size:22px;font-weight:800;color:#2ecc71;letter-spacing:1px;">🌿 Farmer's Basket</div>
          <div style="font-size:12px;color:#aaa;margin-top:4px;">Fresh produce, delivered with care</div>
        </td>
      </tr>

      {{-- Status Banner --}}
      <tr>
        <td style="background:#2ecc71;padding:16px 32px;text-align:center;">
          <div style="font-size:18px;font-weight:700;color:#fff;">✅ Order Confirmed!</div>
        </td>
      </tr>

      {{-- Body --}}
      <tr>
        <td style="padding:28px 32px;">

          <p style="font-size:15px;margin:0 0 20px;">
            Dear <strong>{{ $order->name ?: 'Customer' }}</strong>,<br>
            Thank you for your order. We've received it and will keep you updated.
          </p>

          {{-- Order Meta --}}
          <table width="100%" cellpadding="8" cellspacing="0" style="background:#f8fffe;border:1px solid #c8e6c9;border-radius:8px;margin-bottom:24px;font-size:13px;">
            <tr>
              <td style="color:#555;width:40%;">Order Number</td>
              <td style="font-weight:700;color:#1a1f2e;">{{ $order->order_number }}</td>
            </tr>
            <tr style="background:#f0fdf4;">
              <td style="color:#555;">Order Date</td>
              <td>{{ $order->created_at->format('d M Y, h:i A') }}</td>
            </tr>
            <tr>
              <td style="color:#555;">Payment Status</td>
              <td>{{ ucfirst($order->payment_status ?? 'Pending') }}</td>
            </tr>
            @if($order->source === 'pos')
            <tr style="background:#f0fdf4;">
              <td style="color:#555;">Order Type</td>
              <td>POS / Counter Sale</td>
            </tr>
            @endif
          </table>

          {{-- Items --}}
          <div style="font-size:13px;font-weight:700;text-transform:uppercase;color:#888;margin-bottom:8px;">Items Ordered</div>
          <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #eee;border-radius:8px;overflow:hidden;margin-bottom:24px;">
            <thead>
              <tr style="background:#f5f5f5;">
                <th style="padding:10px 14px;text-align:left;font-size:12px;color:#888;font-weight:600;">Product</th>
                <th style="padding:10px 14px;text-align:center;font-size:12px;color:#888;font-weight:600;">Qty</th>
                <th style="padding:10px 14px;text-align:right;font-size:12px;color:#888;font-weight:600;">Price</th>
              </tr>
            </thead>
            <tbody>
              @foreach($order->orderItems as $i => $item)
              <tr style="{{ $i % 2 === 0 ? 'background:#fff' : 'background:#fafafa' }}">
                <td style="padding:10px 14px;font-size:13px;">
                  {{ $item->product?->name ?? 'Product' }}
                  @if($item->variant_label)
                    <br><span style="font-size:11px;color:#888;">{{ $item->variant_label }}</span>
                  @endif
                </td>
                <td style="padding:10px 14px;text-align:center;font-size:13px;color:#555;">× {{ $item->quantity }}</td>
                <td style="padding:10px 14px;text-align:right;font-size:13px;font-weight:600;">Rs {{ number_format($item->price * $item->quantity, 0) }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>

          {{-- Totals --}}
          <table width="100%" cellpadding="6" cellspacing="0" style="margin-bottom:24px;font-size:13px;">
            <tr>
              <td style="color:#555;text-align:right;padding-right:12px;">Subtotal</td>
              <td style="text-align:right;width:120px;">Rs {{ number_format($order->subtotal, 2) }}</td>
            </tr>
            <tr>
              <td style="color:#555;text-align:right;padding-right:12px;">Tax</td>
              <td style="text-align:right;">Rs {{ number_format($order->tax, 2) }}</td>
            </tr>
            @if($order->discount > 0)
            <tr>
              <td style="color:#2ecc71;text-align:right;padding-right:12px;">Discount</td>
              <td style="text-align:right;color:#2ecc71;">-Rs {{ number_format($order->discount, 2) }}</td>
            </tr>
            @endif
            <tr>
              <td colspan="2"><hr style="border:none;border-top:2px solid #eee;margin:4px 0;"></td>
            </tr>
            <tr>
              <td style="text-align:right;padding-right:12px;font-weight:700;font-size:15px;">TOTAL</td>
              <td style="text-align:right;font-weight:800;font-size:15px;color:#1a1f2e;">Rs {{ number_format($order->total, 2) }}</td>
            </tr>
          </table>

          {{-- Delivery Address --}}
          @if($order->address && $order->source !== 'pos')
          <div style="background:#f8f9fa;border-radius:8px;padding:14px 16px;margin-bottom:24px;font-size:13px;">
            <div style="font-weight:700;margin-bottom:6px;color:#555;font-size:12px;text-transform:uppercase;">Delivery Address</div>
            <div>{{ $order->name }}</div>
            <div>{{ $order->address }}{{ $order->city ? ', '.$order->city : '' }}</div>
            @if($order->phone)<div>📞 {{ $order->phone }}</div>@endif
          </div>
          @endif

          <p style="font-size:13px;color:#888;margin:0;">
            Questions? Contact us at <a href="tel:+923017147110" style="color:#2ecc71;">+92 301 7147110</a>
          </p>
        </td>
      </tr>

      {{-- Footer --}}
      <tr>
        <td style="background:#f5f5f5;padding:20px 32px;text-align:center;font-size:11px;color:#aaa;">
          © {{ date('Y') }} Farmer's Basket. All rights reserved.<br>
          Fresh produce, delivered with care.
        </td>
      </tr>

    </table>
  </td></tr>
</table>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Update</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f8;font-family:'Segoe UI',Arial,sans-serif;color:#1a1f2e;">

@php
$statusConfig = [
    'confirmed' => ['color' => '#2ecc71', 'bg' => '#2ecc71', 'icon' => '✅', 'label' => 'Order Confirmed'],
    'packed'    => ['color' => '#3498db', 'bg' => '#3498db', 'icon' => '📦', 'label' => 'Order Packed'],
    'shipped'   => ['color' => '#9b59b6', 'bg' => '#9b59b6', 'icon' => '🚚', 'label' => 'Order Shipped'],
    'delivered' => ['color' => '#27ae60', 'bg' => '#27ae60', 'icon' => '🎉', 'label' => 'Order Delivered'],
    'canceled'  => ['color' => '#e74c3c', 'bg' => '#e74c3c', 'icon' => '❌', 'label' => 'Order Canceled'],
    'returned'  => ['color' => '#e67e22', 'bg' => '#e67e22', 'icon' => '↩️', 'label' => 'Return Accepted'],
];
$cfg   = $statusConfig[$order->status] ?? ['color' => '#888', 'bg' => '#888', 'icon' => '📋', 'label' => ucfirst($order->status)];
$statusMessages = [
    'confirmed' => 'Great news! Your order has been confirmed and our team is preparing it for you.',
    'packed'    => 'Your order has been carefully packed and is ready for dispatch.',
    'shipped'   => 'Your order is on its way! Our delivery partner is heading to you.',
    'delivered' => 'Your order has been delivered successfully. We hope you enjoy your fresh produce!',
    'canceled'  => 'Your order has been canceled. If you have any questions, please contact us.',
    'returned'  => 'Your return request has been accepted. We will process your refund shortly.',
];
$bodyMessage = $statusMessages[$order->status] ?? 'Your order status has been updated.';
@endphp

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
        <td style="background:{{ $cfg['bg'] }};padding:16px 32px;text-align:center;">
          <div style="font-size:18px;font-weight:700;color:#fff;">{{ $cfg['icon'] }} {{ $cfg['label'] }}</div>
        </td>
      </tr>

      {{-- Body --}}
      <tr>
        <td style="padding:28px 32px;">

          <p style="font-size:15px;margin:0 0 20px;">
            Dear <strong>{{ $order->name ?: 'Customer' }}</strong>,<br>
            {{ $bodyMessage }}
          </p>

          {{-- Order Info --}}
          <table width="100%" cellpadding="8" cellspacing="0" style="background:#f8f9fa;border:1px solid #eee;border-radius:8px;margin-bottom:24px;font-size:13px;">
            <tr>
              <td style="color:#555;width:40%;">Order Number</td>
              <td style="font-weight:700;">{{ $order->order_number }}</td>
            </tr>
            <tr style="background:#fff;">
              <td style="color:#555;">Order Date</td>
              <td>{{ $order->created_at->format('d M Y') }}</td>
            </tr>
            <tr>
              <td style="color:#555;">Total Amount</td>
              <td style="font-weight:700;">Rs {{ number_format($order->total, 2) }}</td>
            </tr>
            <tr style="background:#fff;">
              <td style="color:#555;">Status</td>
              <td>
                <span style="background:{{ $cfg['color'] }};color:#fff;padding:2px 10px;border-radius:20px;font-size:12px;font-weight:600;">
                  {{ $cfg['label'] }}
                </span>
              </td>
            </tr>
            @if($order->status === 'shipped' && $order->tracking_number)
            <tr>
              <td style="color:#555;">Tracking Number</td>
              <td style="font-weight:700;">{{ $order->tracking_number }}</td>
            </tr>
            @endif
            @if($order->status === 'shipped' && $order->courier_name)
            <tr style="background:#fff;">
              <td style="color:#555;">Courier</td>
              <td>{{ $order->courier_name }}</td>
            </tr>
            @endif
            @if($order->status === 'shipped' && $order->estimated_delivery_date)
            <tr>
              <td style="color:#555;">Est. Delivery</td>
              <td>{{ \Carbon\Carbon::parse($order->estimated_delivery_date)->format('d M Y') }}</td>
            </tr>
            @endif
          </table>

          {{-- Items summary --}}
          <div style="font-size:13px;font-weight:700;text-transform:uppercase;color:#888;margin-bottom:8px;">Items</div>
          <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #eee;border-radius:8px;overflow:hidden;margin-bottom:24px;">
            @foreach($order->orderItems as $i => $item)
            <tr style="{{ $i % 2 === 0 ? 'background:#fff' : 'background:#fafafa' }}">
              <td style="padding:9px 14px;font-size:13px;">
                {{ $item->product?->name ?? 'Product' }}
                @if($item->variant_label)
                  <span style="font-size:11px;color:#888;"> ({{ $item->variant_label }})</span>
                @endif
              </td>
              <td style="padding:9px 14px;text-align:right;font-size:13px;font-weight:600;white-space:nowrap;">
                × {{ $item->quantity }}
              </td>
            </tr>
            @endforeach
          </table>

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

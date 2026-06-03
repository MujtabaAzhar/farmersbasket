@extends('layouts.pos')

@push('styles')
<style>
    /* ── Screen Receipt ─────────────────────────────────────────── */
    .receipt-wrapper {
        max-width: 420px; margin: 20px auto; background: #fff;
        border: 1px solid #e0e0e0; border-radius: 10px; overflow: hidden;
    }
    .receipt-header { background: #ffffff; color: #000000; text-align: center; padding: 18px 16px; border-bottom: 1px solid black; }
    .receipt-header .brand { font-size: 18px; font-weight: 800; color: #2ecc71; }
    .receipt-header .sub { font-size: 11px; opacity: .7; margin-top: 2px; }
    .receipt-body { padding: 16px; }
    .receipt-meta { display: flex; justify-content: space-between; font-size: 11px; color: #888; margin-bottom: 12px; }
    .receipt-divider { border: none; border-top: 1px dashed #ccc; margin: 10px 0; }
    .receipt-item { display: flex; gap: 6px; padding: 4px 0; font-size: 13px; }
    .receipt-item .r-name { flex: 1; }
    .receipt-item .r-qty { color: #888; white-space: nowrap; }
    .receipt-item .r-price { font-weight: 600; white-space: nowrap; min-width: 70px; text-align: right; }
    .receipt-total-row { display: flex; justify-content: space-between; font-size: 12px; padding: 2px 0; color: #555; }
    .receipt-total-row.grand { font-size: 16px; font-weight: 800; color: #1a1f2e; margin-top: 6px; }
    .receipt-payment { background: #f0fdf4; border-radius: 8px; padding: 10px 12px; margin-top: 10px; font-size: 12px; }
    .receipt-payment .pm-label { font-weight: 700; color: #2ecc71; font-size: 13px; }
    .receipt-footer { text-align: center; color: #aaa; font-size: 11px; padding: 12px; background: #fafafa; }
    .gift-badge { background: #fff3cd; border-radius: 8px; padding: 8px 12px; margin-bottom: 10px; font-size: 12px; }
    .gift-badge strong { color: #e67e22; }
    .btn-print    { background: #2ecc71; color: #fff; border: none; border-radius: 8px; padding: 10px 20px; font-weight: 700; cursor: pointer; }
    .btn-thermal  { background: #8e44ad; color: #fff; border: none; border-radius: 8px; padding: 10px 20px; font-weight: 700; cursor: pointer; }
    .btn-sticker  { background: #e67e22; color: #fff; border: none; border-radius: 8px; padding: 10px 20px; font-weight: 700; cursor: pointer; }
    .btn-new-sale { background: #1a1f2e; color: #fff; border: none; border-radius: 8px; padding: 10px 20px; font-weight: 700; cursor: pointer; }
    .action-bar   { display: flex; gap: 10px; justify-content: center; padding: 16px; border-top: 1px solid #eee; flex-wrap: wrap; }
    .bo {font-weight: 800; color: #000000; font-size: 12px;};

    @media print {
        .action-bar, .pos-topbar { display: none !important; }
        .receipt-wrapper { border: none; max-width: 100%; margin: 0; }
        body { background: #fff !important; }
    }
</style>
@endpush

@section('content')
<div style="flex: 1; overflow-y: auto; padding: 16px; background: #f0f2f5;">

    {{-- ── Screen Receipt ──────────────────────────────────────────── --}}
    <div class="receipt-wrapper">
        <div class="receipt-header">
            <div class="brand"><img src="{{ asset('images/logo/logo.png') }}" alt="Logo" height="50"></div>
            @if($order->branch)
                <div class="sub">{{ $order->branch->name }} | {{ $order->branch->address }}</div>
            @endif
            <div class="sub" style="margin-top:4px;">SALES RECEIPT</div>
        </div>

        <div class="receipt-body">
            <div class="receipt-meta">
                <span class="bo">Order # {{ $order->order_number }}</span>
                <span>{{ $order->created_at->format('d M Y, h:i A') }}</span>
            </div>
             <div class="receipt-meta" style="margin-top:-8px;">
                <span>Customer Name: {{ $order->user?->name ?? $order->name ?? 'N/A' }}</span>
                <span>Customer Phone: {{ $order->user?->mobile ?? $order->phone ?? 'N/A' }}</span>
            </div>

            <hr class="receipt-divider">

            @if($order->giftOrder)
            <div class="gift-badge">
                <strong>GIFT ORDER</strong><br>
                From: {{ $order->giftOrder->sender_name }} ({{ $order->giftOrder->sender_phone }})<br>
                To: {{ $order->giftOrder->receiver_name }} ({{ $order->giftOrder->receiver_phone }})<br>
                @if($order->giftOrder->gift_message)
                    <em>"{{ $order->giftOrder->gift_message }}"</em>
                @endif
            </div>
            @endif

            @foreach($order->orderItems as $item)
            <div class="receipt-item">
                <span class="r-name">
                    {{ $item->product?->name ?? 'Product #'.$item->product_id }}
                    @if($item->variant_label)
                        <span style="display:block;font-size:11px;color:#888;">{{ $item->variant_label }}  <span class="r-qty">× {{ $item->quantity }}</span></span>
                    @endif
                </span>
               
                <span class="r-price">Rs {{ number_format($item->price * $item->quantity, 0) }}</span>
            </div>
            @endforeach

            <hr class="receipt-divider">

            <div class="receipt-total-row"><span>Subtotal</span><span>Rs {{ number_format($order->subtotal, 2) }}</span></div>
            <div class="receipt-total-row"><span>Tax</span><span>Rs {{ number_format($order->tax, 2) }}</span></div>
            @if($order->shipping > 0)
            <div class="receipt-total-row">
                <span>Shipping{{ $order->courier_name ? ' (' . $order->courier_name . ')' : '' }}</span>
                <span>Rs {{ number_format($order->shipping, 2) }}</span>
            </div>
            @endif
            @if($order->discount > 0)
            <div class="receipt-total-row"><span>Discount</span><span>-Rs {{ number_format($order->discount, 2) }}</span></div>
            @endif
            <div class="receipt-total-row grand"><span>TOTAL</span><span>Rs {{ number_format($order->total, 2) }}</span></div>

            @if($order->posPayment)
            <div class="receipt-payment">
                <div class="pm-label">
                    {{ strtoupper(str_replace('_', ' ', $order->posPayment->method)) }}
                    @if($order->posPayment->online_platform)
                        ({{ ucfirst($order->posPayment->online_platform) }})
                    @endif
                </div>
                @if($order->posPayment->method === 'cash' && $order->posPayment->cash_received)
                    <div>Cash Received: Rs {{ number_format($order->posPayment->cash_received, 2) }}</div>
                    <div>Change: Rs {{ number_format($order->posPayment->change_given ?? 0, 2) }}</div>
                @elseif($order->posPayment->reference_no)
                    <div>Ref: {{ $order->posPayment->reference_no }}</div>
                @endif
            </div>
            @endif

            @if($order->order_note)
            <div style="margin-top:10px; font-size:12px; color:#888;">
                Note: {{ $order->order_note }}
            </div>
            @endif
        </div>

        <div class="receipt-footer">
            Thank you for shopping at Farmer's Basket!<br>
            Fresh produce, delivered with care.<br><br>
            For orders, support, or any queries,<br>
            feel free to contact us at:<br>
            📞 03-111-222-384
        </div>

        <div class="action-bar">
            <button class="btn-print"    onclick="printScreenReceipt()">🖨 Print Receipt</button>
            {{-- <button class="btn-thermal"  onclick="printThermal()">🧾 Print Thermal</button>  --}}
            <button class="btn-sticker"  onclick="printStickers()">📦 Print Stickers</button>
            <button class="btn-new-sale" onclick="window.location='{{ route('pos.index') }}'">New Sale</button>
        </div>
    </div>

    {{-- ── Thermal Slip (hidden, server-rendered, read by printThermal()) ─ --}}
    <div id="thermal-slip" style="display:none;">

        {{-- Header --}}
        <div class="tc">
            <div class="bold xl">FARMER'S BASKET</div>
            @if($order->branch)
                <div class="sm">{{ $order->branch->name }}</div>
                @if($order->branch->address)
                    <div class="sm">{{ $order->branch->address }}</div>
                @endif
        
            @endif
        </div>
        <div class="div-solid"></div>
        <div class="tc bold">CUSTOMER RECEIPT</div>
        <div class="div-dash"></div>

        {{-- Order meta --}}
        <div class="row sm">
            <span>Order#: {{ $order->order_number }}</span>
            <span>{{ $order->created_at->format('d M Y, h:i A') }}</span>
        </div>
        <div class="row sm">
            <span>{{ $order->user?->name ?? $order->name ?? 'N/A' }}</span>
            <span>{{ $order->user?->mobile ?? $order->phone ?? 'N/A' }}</span>
        </div>

        {{-- Gift order --}}
        @if($order->giftOrder)
        <div class="div-dash"></div>
        <div class="gift-box sm">
            <div class="tc bold">** GIFT ORDER **</div>
            <div>From : {{ $order->giftOrder->sender_name }} {{ $order->giftOrder->sender_phone }}</div>
            <div>To   : {{ $order->giftOrder->receiver_name }} {{ $order->giftOrder->receiver_phone }}</div>
            @if($order->giftOrder->gift_message)
                <div>Msg  : "{{ $order->giftOrder->gift_message }}"</div>
            @endif
        </div>
        @endif

        {{-- Items --}}
        <div class="div-dash"></div>
        @foreach($order->orderItems as $item)
        <div class="item-block">
            <div class="item-name">{{ $item->product?->name ?? 'Product #'.$item->product_id }}</div>
            @if($item->variant_label)
                <div class="item-var">({{ $item->variant_label }})</div>
            @endif
            <div class="item-line">
                <span>x{{ $item->quantity }} @ Rs {{ number_format($item->price, 0) }}</span>
                <span class="bold">Rs {{ number_format($item->price * $item->quantity, 0) }}</span>
            </div>
        </div>
        @endforeach

        {{-- Totals --}}
        <div class="div-dash"></div>
        <div class="row sm"><span class="lbl">Subtotal</span><span>Rs {{ number_format($order->subtotal, 2) }}</span></div>
        <div class="row sm"><span class="lbl">Tax</span><span>Rs {{ number_format($order->tax, 2) }}</span></div>
        @if($order->shipping > 0)
            <div class="row sm">
                <span class="lbl">Shipping{{ $order->courier_name ? ' (' . $order->courier_name . ')' : '' }}</span>
                <span>Rs {{ number_format($order->shipping, 2) }}</span>
            </div>
        @endif
        @if($order->discount > 0)
            <div class="row sm"><span class="lbl">Discount</span><span>-Rs {{ number_format($order->discount, 2) }}</span></div>
        @endif
        <div class="div-solid"></div>
        <div class="row bold lg"><span class="lbl">TOTAL</span><span>Rs {{ number_format($order->total, 2) }}</span></div>
        <div class="div-solid"></div>

        {{-- Payment --}}
        @if($order->posPayment)
        <div class="payment-box sm">
            <div class="bold">
                {{ strtoupper(str_replace('_', ' ', $order->posPayment->method)) }}
                @if($order->posPayment->online_platform)
                    ({{ ucfirst($order->posPayment->online_platform) }})
                @endif
            </div>
            @if($order->posPayment->method === 'cash' && $order->posPayment->cash_received)
                <div class="row">
                    <span class="lbl">Cash Received</span>
                    <span>Rs {{ number_format($order->posPayment->cash_received, 2) }}</span>
                </div>
                <div class="row">
                    <span class="lbl">Change</span>
                    <span>Rs {{ number_format($order->posPayment->change_given ?? 0, 2) }}</span>
                </div>
            @elseif($order->posPayment->reference_no)
                <div>Ref: {{ $order->posPayment->reference_no }}</div>
            @endif
        </div>
        @endif

        {{-- Note --}}
        @if($order->order_note)
            <div class="div-dash"></div>
            <div class="sm">Note: {{ $order->order_note }}</div>
        @endif

        {{-- Footer --}}
        <div class="div-dash"></div>
        <div class="tc sm">
            <div class="bold">Thank you for shopping!</div>
            <div>Farmer's Basket</div>
            <div>Fresh produce, delivered with care.</div>
            <div style="margin-top:4px;">03-111-222-384</div>
            <div style="margin-top:6px;font-size:7pt;">{{ $order->order_number }} &bull; {{ $order->created_at->format('d M Y H:i') }}</div>
        </div>

    </div>{{-- #thermal-slip --}}

</div>
@endsection

@push('scripts')
<script>
// ── Sticker data (PHP → JS) ───────────────────────────────────────────────────
@php
    $gift = $order->giftOrder;
    $stickerPayload = [
        'orderNumber' => $order->order_number,
        'createdAt'   => $order->created_at->format('n/j/y, g:i A'),
        'cashier'     => auth()->user()?->name ?? 'Staff',
        'isGift'      => (bool) $gift,
        'to' => $gift
            ? ['name' => $gift->receiver_name, 'phone' => $gift->receiver_phone,
               'address' => $gift->receiver_address, 'city' => $gift->receiver_city]
            : ['name' => $order->name, 'phone' => $order->phone,
               'address' => $order->address, 'city' => $order->city],
        'from' => $gift
            ? ['name' => $gift->sender_name, 'phone' => $gift->sender_phone,
               'address' => $gift->sender_address ?? '', 'city' => $gift->sender_city ?? '']
            : ['name'    => ($order->branch?->name ?: "FARMER'S BASKET"),
               'address' => ($order->branch?->address ?: ''),
               'phone'   => '03-111-222-384',
               'city'    => ($order->branch?->city ?: 'Lahore')],
        'message' => $gift?->gift_message ?? '',
        'items'   => $order->orderItems->map(function ($i) {
            return [
                'name'    => $i->product?->name ?? ('Product #' . $i->product_id),
                'brand'   => $i->product?->brand?->name ?? '',
                'variant' => $i->variant_label ?? '',
                'qty'     => $i->quantity,
            ];
        })->values()->all(),
    ];
@endphp
var stickerData = @json($stickerPayload);

// ── Receipt data (PHP → JS) ──────────────────────────────────────────────────
@php
    $receiptPayload = [
        'orderNumber'   => $order->order_number,
        'createdAt'     => $order->created_at->format('d/m/Y H:i'),
        'cashier'       => auth()->user()?->name ?? 'Staff',
        'branch'        => $order->branch?->name ?: "FARMER'S BASKET",
        'paymentMethod' => $order->posPayment
                            ? strtoupper(str_replace('_', ' ', $order->posPayment->method))
                            : 'N/A',
        'paymentDetail' => $order->posPayment?->online_platform
                            ? ucfirst($order->posPayment->online_platform)
                            : null,
        'cashReceived'  => $order->posPayment?->cash_received ?? null,
        'changeGiven'   => $order->posPayment?->change_given  ?? null,
        'to' => $gift
            ? ['name' => $gift->receiver_name, 'phone' => $gift->receiver_phone,
               'address' => $gift->receiver_address ?? '', 'city' => $gift->receiver_city ?? '']
            : ['name' => $order->name ?? '', 'phone' => $order->phone ?? '',
               'address' => $order->address ?? '', 'city' => $order->city ?? ''],
        'from' => $gift
            ? ['name'  => $gift->sender_name  ?: "FARMER'S BASKET",
               'phone' => $gift->sender_phone ?: '03-111-222-384',
               'city'  => $gift->sender_city  ?: '']
            : ['name'  => $order->branch?->name ?: "FARMER'S BASKET",
               'phone' => '03-111-222-384',
               'city'  => $order->branch?->city ?: 'Lahore'],
        'items'    => $order->orderItems->map(function ($i) {
            return [
                'name'    => $i->product?->name ?? ('Product #' . $i->product_id),
                'brand'   => $i->product?->brand?->name ?? '',
                'variant' => $i->variant_label ?? '',
                'qty'     => $i->quantity,
                'price'   => $i->price,
                'total'   => $i->price * $i->quantity,
            ];
        })->values()->all(),
        'totalQty'    => $order->orderItems->sum('quantity'),
        'subtotal'    => $order->subtotal,
        'tax'         => $order->tax      ?? 0,
        'shipping'    => $order->shipping ?? 0,
        'discount'    => $order->discount ?? 0,
        'total'       => $order->total,
        'courierName' => $order->courier_name ?? '',
        'orderNote'   => $order->order_note  ?? '',
        'logoUrl'     => asset('images/logo/logo.png'),
    ];
@endphp
var receiptData = @json($receiptPayload);

// ── Sticker printer ───────────────────────────────────────────────────────────
function escH(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

function buildSticker(item, stickerIdx, totalStickers) {
    var d = stickerData;
    var s = '<div class="sticker">';

    // Large invoice number (centered) with total-quantity suffix
    s += '<div class="s-inv-lg">Invoice No: ' + escH(d.orderNumber) + ' / ' + totalStickers + '</div>';

    // Cashier / user line with brand on the right
    s += '<div class="s-user-row">'
       + '<span>USER: ' + escH(d.cashier) + '</span>'
       + (item.brand ? '<span>Brand: <strong>' + escH(item.brand) + '</strong></span>' : '')
       + '</div>';

    // Single combined box for TO and FROM
    var addrClass = (d.to.address && d.to.address.length > 45) ? 's-detail-sm' : 's-detail';
    s += '<div class="s-combined-box">';

    // TO section inside combined box
    s += '<div class="s-box-title">TO</div>';
    s += '<div class="s-to-name">' + escH(d.to.name) + '</div>';
    if (d.to.address) s += '<div class="' + addrClass + '">Delivery Address: ' + escH(d.to.address) + '</div>';
    s += '<div class="s-detail">Contact: ' + escH(d.to.phone) + '</div>';
    if (d.to.city) s += '<div class="s-city-bar">City: ' + escH(d.to.city).toUpperCase() + '</div>';

    // Divider between TO and FROM
    s += '<div class="s-inner-divider"></div>';

    // FROM section inside combined box
    s += '<div class="s-box-title">FROM</div>';
    s += '<div class="s-from-name">' + escH(d.from.name) + '</div>';
    s += '<div class="s-detail">Contact: ' + escH(d.from.phone) + '</div>';
    if (d.from.city) s += '<div class="s-city-bar">City: ' + escH(d.from.city) + '</div>';

    s += '</div>'; // .s-combined-box

    // Helpline only — no footer row
    s += '<div class="s-helpline">In case of any query please contact our customer Helpline 03-111-222-384</div>';

    s += '</div>'; // .sticker
    return s;
}

function printStickers() {
    var css = [
        '* { margin:0; padding:0; box-sizing:border-box; }',
        'body { font-family:Arial,Helvetica,sans-serif; color:#000; background:#fff; }',
        '@page { size:4in 4in; margin:0; }',
        '.sticker {',
        '  width:4in; height:4in;',
        '  padding:10px 14px 8px;',
        '  display:flex; flex-direction:column; gap:5px;',
        '  page-break-after:always; overflow:hidden;',
        '}',
        '.sticker:last-child { page-break-after:avoid; }',
        '.s-inv-lg        { font-size:14pt; font-weight:bold; margin-top:1px; text-align:center; }',
        '.s-helpline      { font-size:9.5pt; font-weight:bold; color:#000; text-align:center; margin-top:1px; padding-top:4px; }',
        '.s-user-row      { display:flex; justify-content:space-between; font-size:9.5pt; font-weight:600; letter-spacing:.3px; }',
        '.s-combined-box  { border:2px solid #000; padding:6px 10px 5px; }',
        '.s-inner-divider { border-top:1px dashed #000; margin:6px 0; }',
        '.s-box-title     { text-align:center; font-weight:bold; font-size:13pt; letter-spacing:2px; margin-bottom:3px; }',
        '.s-to-name       { font-size:13pt; font-weight:bold; line-height:1.15; }',
        '.s-from-name     { font-size:13pt; font-weight:700; }',
        '.s-detail        { font-size:13pt; margin:2px 0; }',
        '.s-detail-sm     { font-size:9pt; margin:2px 0; }',
        '.s-city-bar      { background:#000; color:#fff; text-align:center; font-weight:bold; font-size:13pt; padding:3px 0; margin-top:5px; letter-spacing:1px; }',
    ].join('\n');

    var totalStickers = stickerData.items.reduce(function(sum, i) { return sum + i.qty; }, 0);

    var html = '';
    var stickerIdx = 0;
    stickerData.items.forEach(function(item) {
        for (var i = 0; i < item.qty; i++) {
            html += buildSticker(item, stickerIdx, totalStickers);
            stickerIdx++;
        }
    });

    if (!html) { alert('No items to print stickers for.'); return; }

    var win = window.open('', '_blank', 'width=600,height=600,scrollbars=yes,resizable=yes');
    win.document.write(
        '<!DOCTYPE html><html><head><meta charset="utf-8">'
        + '<title>Stickers &mdash; {{ $order->order_number }}</title>'
        + '<style>' + css + '</style></head><body>'
        + html + '</body></html>'
    );
    win.document.close();
    win.focus();
    setTimeout(function(){ win.print(); }, 400);
}

// ── Screen Receipt: 2 slips on 1 A4 page, matching reference layout ──────────
function printScreenReceipt() {
    var d = receiptData;

    function fmt(n) {
        return 'Rs ' + parseFloat(n || 0).toLocaleString('en-PK', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function buildSlip(copyLabel) {
        var addrClass = (d.to.address && d.to.address.length > 40) ? 'r-sm' : 'r-det';

        // ── Header row
        var header = '<div class="r-hdr">'
            + '<div class="r-hdr-left">'
            +   '<img src="' + escH(d.logoUrl) + '" class="r-logo" alt="Logo">'
            +   '<div class="r-hdr-meta">'
            +     '<div class="r-meta-line">Date: ' + escH(d.createdAt) + ' &nbsp;|&nbsp; USER: ' + escH(d.cashier) + ' &nbsp;|&nbsp; <strong>' + escH(copyLabel) + '</strong></div>'
            +     '<div class="r-meta-line">Payment Method : <strong>' + escH(d.paymentMethod) + (d.paymentDetail ? ' (' + escH(d.paymentDetail) + ')' : '') + '</strong></div>'
            +   '</div>'
            + '</div>'
            + '<div class="r-hdr-right">'
            +   '<div class="r-banner">' + escH(d.courierName || 'COUNTER SALE') + '</div>'
            +   '<div class="r-inv-block">Invoice No:<br><strong>' + escH(d.orderNumber) + ' / ' + d.totalQty + '</strong></div>'
            + '</div>'
            + '</div>';

        // ── TO / FROM side-by-side
        var tofrom = '<div class="r-tofrom">'
            // TO
            + '<div class="r-to-col">'
            +   '<div class="r-col-hdr">TO</div>'
            +   '<div class="r-name">' + escH(d.to.name) + '</div>'
            +   (d.to.address ? '<div class="' + addrClass + '">Delivery Address: ' + escH(d.to.address) + '</div>' : '')
            +   '<div class="r-det">Contact: ' + escH(d.to.phone) + '</div>'
            +   (d.to.city ? '<div class="r-city">City: ' + escH(d.to.city).toUpperCase() + '</div>' : '')
            + '</div>'
            // FROM
            + '<div class="r-from-col">'
            +   '<div class="r-col-hdr">FROM</div>'
            +   '<div class="r-name">' + escH(d.from.name) + '</div>'
            +   '<div class="r-det">Address:</div>'
            +   '<div class="r-det">Contact: ' + escH(d.from.phone) + '</div>'
            +   (d.from.city ? '<div class="r-city">City: ' + escH(d.from.city) + '</div>' : '')
            + '</div>'
            + '</div>';

        // ── Order details (full width, below TO/FROM)
        var itemLines = '';
        d.items.forEach(function(item) {
            var label = escH(item.name) + (item.variant ? ' (' + escH(item.variant) + ')' : '');
            var brandTag = item.brand ? ' <span class="r-brand">' + escH(item.brand) + '</span>' : '';
            itemLines += '<tr>'
                + '<td class="r-dt-lbl">' + label + brandTag + '</td>'
                + '<td class="r-dt-mid">' + item.qty + ' &times; ' + fmt(item.price) + '</td>'
                + '<td class="r-dt-val">' + fmt(item.total) + '</td>'
                + '</tr>';
        });

        var deliveryRow = '';
        if (d.shipping > 0) {
            var courierLabel = d.courierName
                ? '<span class="r-courier-banner">' + escH(d.courierName) + '</span>'
                : '';
            deliveryRow = '<tr>'
                + '<td class="r-dt-lbl">Delivery By: ' + courierLabel + '</td>'
                 + '<td class="r-dt-mid">' + d.totalQty + ' &times; ' + fmt(d.shipping / d.totalQty) + '</td>'
                + '<td class="r-dt-val">' + fmt(d.shipping) + '</td>'
                + '</tr>';
        }

        var discRow = d.discount > 0
            ? '<tr><td class="r-dt-lbl">Disc Amt (%):</td><td class="r-dt-mid"></td><td class="r-dt-val">- ' + fmt(d.discount) + '</td></tr>'
            : '<tr><td class="r-dt-lbl">Disc Amt (%):</td><td class="r-dt-mid"></td><td class="r-dt-val">' + fmt(0) + '</td></tr>';

        var details = '<div class="r-details">'
            + '<table class="r-dt-table"><tbody>'
            +   '<tr><td class="r-dt-branch" colspan="3">' + escH(d.branch) + '</td></tr>'
            +   '<tr><td class="r-dt-lbl">Total Item(s): ' + d.totalQty + '</td><td class="r-dt-mid"></td><td class="r-dt-val"></td></tr>'
            +   itemLines
            +   '<tr class="r-divrow"><td colspan="3"><div class="r-thin-line"></div></td></tr>'
            +   '<tr><td class="r-dt-lbl">Sub Total:</td><td class="r-dt-mid"></td><td class="r-dt-val">' + fmt(d.subtotal) + '</td></tr>'
            +   discRow
            +   deliveryRow
            +   '<tr class="r-divrow"><td colspan="3"><div class="r-thin-line"></div></td></tr>'
            +   '<tr><td class="r-dt-grand">Grand Total:</td><td class="r-dt-mid"></td><td class="r-dt-grand r-dt-val">' + fmt(d.total) + '</td></tr>'
            +   '<tr><td class="r-dt-lbl">Paid Amount:</td><td class="r-dt-mid"></td><td class="r-dt-val">' + fmt(d.total) + '</td></tr>'
            + '</tbody></table>'
            + '</div>';

        // ── Footer
        var footer = '<div class="r-footer">'
            + 'For Inquiries &amp; suggestions Please Contact: Help line: <strong>03-111-222-384</strong><br>'
            + 'Thanks For Visiting At Farmer\'s Basket &mdash; For any Query and Complaint Call Us at 03-111-222-384'
            + '</div>';

        return '<div class="slip">' + header + tofrom + details + footer + '</div>';
    }

    var css = [
        '* { margin:0; padding:0; box-sizing:border-box; }',
        'body { font-family:Arial,Helvetica,sans-serif; font-size:9.5pt; color:#000; background:#fff; width:210mm; }',
        '@page { size:A4 portrait; margin:0; }',

        // Two slips stacked — each exactly half A4
        '.slip     { width:210mm; height:148.5mm; padding:4mm 6mm 3mm; display:flex; flex-direction:column; overflow:hidden; }',
        '.cut-line { width:210mm; height:4mm; display:flex; align-items:center; justify-content:center; font-size:8pt; color:#aaa; letter-spacing:2px; border-top:1px dashed #bbb; border-bottom:1px dashed #bbb; }',

        // ── HEADER
        '.r-hdr       { display:flex; align-items:stretch; gap:4mm; border-bottom:1.5px solid #000; padding-bottom:2mm; margin-bottom:2mm; }',
        '.r-hdr-left  { display:flex; align-items:center; gap:3mm; flex:1; }',
        '.r-logo      { height:34px; width:auto; flex-shrink:0; }',
        '.r-hdr-meta  { display:flex; flex-direction:column; gap:2px; }',
        '.r-meta-line { font-size:8.5pt; color:#222; }',
        '.r-hdr-right { display:flex; flex-direction:column; align-items:flex-end; justify-content:center; min-width:65mm; }',
        '.r-banner    { background:#000; color:#fff; font-size:12pt; font-weight:bold; letter-spacing:1px; padding:4px 8px; text-align:center; width:100%; }',
        '.r-inv-block { font-size:9.5pt; text-align:right; margin-top:2px; line-height:1.4; }',

        // ── TO / FROM side by side
        '.r-tofrom    { display:flex; border:1.5px solid #000; margin-bottom:2mm; }',
        '.r-to-col    { flex:1; padding:2mm 3mm; border-right:1.5px solid #000; }',
        '.r-from-col  { flex:1; padding:2mm 3mm; }',
        '.r-col-hdr   { text-align:center; font-weight:bold; font-size:11pt; letter-spacing:1px; border-bottom:1px solid #000; margin-bottom:1.5mm; padding-bottom:2px; }',
        '.r-name      { font-size:11pt; font-weight:bold; line-height:1.3; }',
        '.r-det       { font-size:9pt; margin:2px 0; }',
        '.r-sm        { font-size:7.5pt; margin:2px 0; }',
        '.r-city      { background:#000; color:#fff; text-align:center; font-weight:bold; font-size:10pt; padding:3px 0; margin-top:2mm; letter-spacing:1px; }',

        // ── ORDER DETAILS
        '.r-details       { flex:1; border:1.5px solid #000; padding:1.5mm 2.5mm; margin-bottom:1.5mm; }',
        '.r-dt-table      { width:100%; border-collapse:collapse; }',
        '.r-dt-table td   { font-size:9pt; padding:2px 3px; vertical-align:middle; }',
        '.r-dt-branch     { font-weight:bold; font-size:10pt; padding-bottom:1.5mm !important; }',
        '.r-dt-lbl        { width:55%; }',
        '.r-dt-mid        { width:25%; text-align:center; color:#333; }',
        '.r-dt-val        { width:20%; text-align:right; font-weight:600; }',
        '.r-dt-grand      { font-weight:bold; font-size:10pt; }',
        '.r-brand         {background:#000; color:#fff; font-weight:bold; font-size:8pt; margin-left:3px; padding:1px 6px; }',
        '.r-thin-line     { border-top:1px solid #000; margin:2px 0; }',
        '.r-courier-banner{ background:#000; color:#fff; font-weight:bold; padding:1px 6px; font-size:8pt; letter-spacing:.5px; }',

        // ── FOOTER
        '.r-footer { text-align:center; font-size:8pt; border-top:1px solid #000; padding-top:1.5mm; line-height:1.6; margin-top:auto; }',
    ].join('\n');

    var slip1 = buildSlip('Customer Slip');
    var cut   = '<div class="cut-line">&#9988; &mdash;&mdash;&mdash;&mdash;&mdash; cut here &mdash;&mdash;&mdash;&mdash;&mdash; &#9988;</div>';
    var slip2 = buildSlip('Counter Copy');

    var win = window.open('', '_blank', 'width=800,height=900,scrollbars=yes,resizable=yes');
    win.document.write(
        '<!DOCTYPE html><html><head><meta charset="utf-8">'
        + '<title>Receipt &mdash; {{ $order->order_number }}</title>'
        + '<style>' + css + '</style></head><body>'
        + slip1 + cut + slip2
        + '</body></html>'
    );
    win.document.close();
    win.focus();
    setTimeout(function () { win.print(); }, 400);
}

// ── Thermal Receipt: 2 copies on the same roll ────────────────────────────────
function printThermal() {
    var css = [
        '* { margin:0; padding:0; box-sizing:border-box; }',
        'body { font-family:"Courier New",Courier,monospace; font-size:9pt; color:#000; background:#fff; width:76mm; }',
        '@page { size:80mm auto; margin:3mm 2mm; }',
        '.tc  { text-align:center; }',
        '.tr  { text-align:right; }',
        '.bold{ font-weight:bold; }',
        '.lg  { font-size:11pt; }',
        '.xl  { font-size:13pt; letter-spacing:1px; }',
        '.sm  { font-size:8pt; }',
        '.div-dash  { border-top:1px dashed #000; margin:5px 0; }',
        '.div-solid { border-top:2px solid #000; margin:5px 0; }',
        '.row { display:flex; justify-content:space-between; margin:2px 0; }',
        '.row .lbl { flex:1; }',
        '.item-block { margin:4px 0; }',
        '.item-name  { font-weight:bold; font-size:9pt; }',
        '.item-var   { font-size:8pt; padding-left:6px; }',
        '.item-line  { display:flex; justify-content:space-between; padding-left:6px; font-size:9pt; }',
        '.payment-box{ border:1px solid #000; padding:4px 6px; margin-top:6px; }',
        '.gift-box   { border:1px dashed #000; padding:4px 6px; margin:4px 0; }',
        '.copy-hdr   { text-align:center; font-weight:bold; font-size:9pt; letter-spacing:2px; border:1px solid #000; padding:3px; margin-bottom:6px; }',
        '.copy-sep   { border-top:2px dashed #000; text-align:center; font-size:8pt; padding:5px 0; margin:8px 0; letter-spacing:1px; }',
    ].join('\n');

    var html         = document.getElementById('thermal-slip').innerHTML;
    var customerCopy = '<div class="copy-hdr">CUSTOMER COPY</div>' + html;
    var separator    = '<div class="copy-sep">- - - - - cut here - - - - -</div>';
    var counterCopy  = '<div class="copy-hdr">COUNTER COPY</div>'  + html;

    var win = window.open('', '_blank', 'width=340,height=700,scrollbars=yes,resizable=yes');
    win.document.write(
        '<!DOCTYPE html><html><head><meta charset="utf-8">'
        + '<title>{{ $order->order_number }}</title>'
        + '<style>' + css + '</style></head><body>'
        + customerCopy + separator + counterCopy
        + '</body></html>'
    );
    win.document.close();
    win.focus();
    setTimeout(function(){ win.print(); }, 400);
}
</script>
@endpush

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
    .btn-new-sale { background: #1a1f2e; color: #fff; border: none; border-radius: 8px; padding: 10px 20px; font-weight: 700; cursor: pointer; }
    .action-bar   { display: flex; gap: 10px; justify-content: center; padding: 16px; border-top: 1px solid #eee; flex-wrap: wrap; }

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
                <span>{{ $order->order_number }}</span>
                <span>{{ $order->created_at->format('d M Y, h:i A') }}</span>
            </div>
            <div class="receipt-meta" style="margin-top:-8px;">
                <span>Cashier: {{ $order->cashier?->name ?? 'N/A' }}</span>
                <span>{{ $order->created_at->format('l') }}</span>
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
                        <span style="display:block;font-size:11px;color:#888;">{{ $item->variant_label }}</span>
                    @endif
                </span>
                <span class="r-qty">× {{ $item->quantity }}</span>
                <span class="r-price">Rs {{ number_format($item->price * $item->quantity, 0) }}</span>
            </div>
            @endforeach

            <hr class="receipt-divider">

            <div class="receipt-total-row"><span>Subtotal</span><span>Rs {{ number_format($order->subtotal, 2) }}</span></div>
            <div class="receipt-total-row"><span>Tax</span><span>Rs {{ number_format($order->tax, 2) }}</span></div>
            @if($order->discount > 0)
            <div class="receipt-total-row"><span>Discount</span><span>-Rs {{ number_format($order->discount, 2) }}</span></div>
            @endif
            <div class="receipt-total-row grand"><span>TOTAL</span><span>Rs {{ number_format($order->total, 2) }}</span></div>

            @if($order->posPayment)
            <div class="receipt-payment">
                <div class="pm-label">{{ strtoupper(str_replace('_', ' ', $order->posPayment->method)) }}</div>
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
            📞 +92 301 7147110
        </div>

        <div class="action-bar">
            <button class="btn-print"    onclick="window.print()">🖨 Print Receipt</button>
            <button class="btn-thermal"  onclick="printThermal()">🧾 Print Thermal</button>
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
            <span>{{ $order->created_at->format('d-M-Y') }}</span>
        </div>
        <div class="row sm">
            <span>Cashier: {{ $order->cashier?->name ?? 'N/A' }}</span>
            <span>{{ $order->created_at->format('h:i A') }}</span>
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
        @if($order->discount > 0)
            <div class="row sm"><span class="lbl">Discount</span><span>-Rs {{ number_format($order->discount, 2) }}</span></div>
        @endif
        <div class="div-solid"></div>
        <div class="row bold lg"><span class="lbl">TOTAL</span><span>Rs {{ number_format($order->total, 2) }}</span></div>
        <div class="div-solid"></div>

        {{-- Payment --}}
        @if($order->posPayment)
        <div class="payment-box sm">
            <div class="bold">{{ strtoupper(str_replace('_', ' ', $order->posPayment->method)) }}</div>
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
            @elseif($order->posPayment->online_platform)
                <div>Via: {{ $order->posPayment->online_platform }}</div>
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
            <div style="margin-top:4px;">+92 301 7147110</div>
            <div style="margin-top:6px;font-size:7pt;">{{ $order->order_number }} &bull; {{ $order->created_at->format('d M Y H:i') }}</div>
        </div>

    </div>{{-- #thermal-slip --}}

</div>
@endsection

@push('scripts')
<script>
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
    ].join('\n');

    var html = document.getElementById('thermal-slip').innerHTML;

    var win = window.open('', '_blank', 'width=340,height=700,scrollbars=yes,resizable=yes');
    win.document.write('<!DOCTYPE html><html><head><meta charset="utf-8"><title>{{ $order->order_number }}</title><style>' + css + '</style></head><body>' + html + '</body></html>');
    win.document.close();
    win.focus();
    setTimeout(function() { win.print(); }, 400);
}
</script>
@endpush

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 9px;
    color: #1a1a1a;
    background: #fff;
}
.page { padding: 18px 22px; }

/* ── Header ── */
.report-header {
    display: table;
    width: 100%;
    border-bottom: 2px solid #1a472a;
    padding-bottom: 8px;
    margin-bottom: 10px;
}
.header-logo  { display: table-cell; vertical-align: middle; width: 54px; }
.header-logo img { width: 48px; height: auto; }
.header-logo-text { font-size: 16px; font-weight: 900; color: #1a472a; }
.header-center { display: table-cell; vertical-align: middle; text-align: center; }
.header-center h1 { font-size: 16px; font-weight: 800; color: #1a472a; letter-spacing: .8px; }
.header-center .sub {
    font-size: 8.5px; color: #555; margin-top: 2px;
    border-top: 1px solid #ddd; padding-top: 3px; margin-top: 4px;
}
.header-right { display: table-cell; vertical-align: middle; text-align: right; width: 110px; }
.header-right .date-box {
    background: #1a472a; color: #fff;
    border-radius: 5px; padding: 5px 9px;
    font-size: 8px; line-height: 1.6;
}

/* ── Section title ── */
.section-title {
    font-size: 8px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .7px; color: #1a472a;
    border-left: 3px solid #1a472a; padding-left: 5px;
    margin: 7px 0 4px;
}

/* ── Summary cards row ── */
.cards-row { display: table; width: 100%; border-spacing: 4px; margin-bottom: 2px; }
.card {
    display: table-cell; border: 1px solid #e2e8f0;
    border-radius: 4px; padding: 5px 6px; text-align: center;
    background: #fafafa; width: 16.6%;
}
.card .v { font-size: 12px; font-weight: 800; color: #1a472a; }
.card .l { font-size: 7.5px; color: #666; margin-top: 1px; }
.card.red { background: #fff1f2; border-color: #fecdd3; }
.card.red .v { color: #be123c; }

/* ── Two-column layout ── */
.two-col { display: table; width: 100%; border-spacing: 6px; }
.col-l   { display: table-cell; width: 52%; vertical-align: top; }
.col-r   { display: table-cell; width: 48%; vertical-align: top; }

/* ── Tables ── */
table { width: 100%; border-collapse: collapse; }
th {
    background: #1a472a; color: #fff;
    padding: 4px 6px; text-align: left;
    font-size: 8px; font-weight: 600; letter-spacing: .2px;
}
th.r, td.r { text-align: right; }
td {
    padding: 3px 6px; border-bottom: 1px solid #ececec;
    font-size: 8px;
}
tr:nth-child(even) td { background: #f8fffe; }
tr.tot td {
    font-weight: 700; background: #f0fdf4 !important;
    border-top: 1px solid #1a472a; border-bottom: 1px solid #1a472a;
    color: #14532d; font-size: 8px;
}

/* ── Net box ── */
.net-box {
    background: #1a472a; color: #fff;
    border-radius: 6px; padding: 8px 14px;
    margin-top: 8px; display: table; width: 100%;
}
.net-box-l { display: table-cell; vertical-align: middle; }
.net-box-r { display: table-cell; vertical-align: middle; text-align: right; }
.net-box .lbl  { font-size: 8px; opacity: .75; margin-bottom: 1px; }
.net-box .val  { font-size: 16px; font-weight: 800; }
.net-box .tiny { font-size: 7.5px; opacity: .65; }
.net-box .exp  { font-size: 11px; font-weight: 700; color: #fca5a5; }
.net-box .net  { font-size: 14px; font-weight: 900; color: #86efac; }
.divider       { border-top: 1px solid rgba(255,255,255,.25); padding-top: 4px; margin-top: 4px; }

/* ── Badge ── */
.badge {
    display: inline-block; padding: 1px 5px;
    border-radius: 8px; font-size: 7.5px; font-weight: 700;
}
.b-pos     { background: #fef9c3; color: #854d0e; }
.b-online  { background: #dbeafe; color: #1e40af; }
.b-website { background: #e0f2fe; color: #075985; }
.b-gift    { background: #fce7f3; color: #9d174d; }
.b-pickup  { background: #dcfce7; color: #166534; }
.b-booking { background: #ede9fe; color: #5b21b6; }
</style>
</head>
<body>
<div class="page">

{{-- ══ HEADER ══ --}}
<div class="report-header">
   
    <div class="header-center">
        <h1>FARMER'S BASKET</h1>
        <div class="sub">
            Daily Summary Report
            &nbsp;·&nbsp;
            {{ $from->format('d M Y') }}@if($from->toDateString() !== $to->toDateString()) – {{ $to->format('d M Y') }}@endif
            @if($filterCashier) &nbsp;·&nbsp; Cashier: {{ $filterCashier->name }} @endif
            @if($filterBranch)  &nbsp;·&nbsp; Branch: {{ $filterBranch->name }} @endif
        </div>
    </div>
  
</div>

{{-- ══ SUMMARY CARDS ══ --}}
<div class="cards-row">
    <div class="card"><div class="v">{{ $totalOrders }}</div><div class="l">Total Orders</div></div>
    <div class="card"><div class="v">Rs {{ number_format($grossSales, 0) }}</div><div class="l">Gross Sales</div></div>
    <div class="card"><div class="v">Rs {{ number_format($totalDiscount, 0) }}</div><div class="l">Discounts</div></div>
    <div class="card"><div class="v">Rs {{ number_format($totalShipping, 0) }}</div><div class="l">Shipping</div></div>
    <div class="card"><div class="v">Rs {{ number_format($netSales, 0) }}</div><div class="l">Net Sales</div></div>
    <div class="card red"><div class="v">Rs {{ number_format($totalExpenses, 0) }}</div><div class="l">Expenses</div></div>
</div>

{{-- ══ CHANNEL + ORDER TYPE (side by side) ══ --}}
<div class="two-col">
    <div class="col-l">
        <div class="section-title">Sales by Channel</div>
        <table>
            <thead><tr><th>Channel</th><th class="r">Orders</th><th class="r">Rs</th><th class="r">%</th></tr></thead>
            <tbody>
                @foreach($bySource as $src => $d)
                <tr>
                    <td>
                        @if($src === 'pos')
                            <span class="badge b-pos">🖥 POS Counter</span>
                        @else
                            <span class="badge b-website">🌐 Website / E-Commerce</span>
                        @endif
                    </td>
                    <td class="r">{{ $d['count'] }}</td>
                    <td class="r">{{ number_format($d['total'], 0) }}</td>
                    <td class="r">{{ $netSales>0 ? number_format(($d['total']/$netSales)*100,1) : 0 }}%</td>
                </tr>
                @endforeach
                <tr class="tot"><td>TOTAL</td><td class="r">{{ $totalOrders }}</td><td class="r">{{ number_format($netSales, 0) }}</td><td class="r">100%</td></tr>
            </tbody>
        </table>
    </div>
    <div class="col-r">
        <div class="section-title">Sales by Order Type</div>
        <table>
            @php $typeLabels=['pickup'=>'Counter Pickup','booking'=>'Booking/Delivery','gift'=>'Gift Order','online'=>'Online Order']; @endphp
            <thead><tr><th>Type</th><th class="r">Orders</th><th class="r">Rs</th></tr></thead>
            <tbody>
                @foreach($byType as $type => $d)
                <tr>
                    <td><span class="badge b-{{ $type }}">{{ $typeLabels[$type] ?? ucfirst($type) }}</span></td>
                    <td class="r">{{ $d['count'] }}</td>
                    <td class="r">{{ number_format($d['total'], 0) }}</td>
                </tr>
                @endforeach
                <tr class="tot"><td>TOTAL</td><td class="r">{{ $totalOrders }}</td><td class="r">{{ number_format($netSales, 0) }}</td></tr>
            </tbody>
        </table>
    </div>
</div>

{{-- ══ PAYMENT METHODS ══ --}}
@if($byPayment->count() || $byWebPayment->count() || $byCashier->count())
<div class="two-col">

    {{-- POS Payments --}}
    @if($byPayment->count())
    <div class="col-l">
        <div class="section-title">🖥 POS Payment Methods</div>
        @php $pmL=['cash'=>'Cash','online_transfer'=>'Online Transfer','credit'=>'Credit/Pending']; @endphp
        <table>
            <thead><tr><th>Method</th><th class="r">Txns</th><th class="r">Amount (Rs)</th></tr></thead>
            <tbody>
                @foreach($byPayment as $m => $d)
                <tr><td>{{ $pmL[$m] ?? ucwords(str_replace('_',' ',$m)) }}</td><td class="r">{{ $d['count'] }}</td><td class="r">{{ number_format($d['total'], 0) }}</td></tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Website Payments --}}
    @if($byWebPayment->count())
    <div class="{{ $byPayment->count() ? 'col-r' : 'col-l' }}">
        <div class="section-title">🌐 Website Payment Methods</div>
        @php $webPmL=['bank_transfer'=>'Bank Transfer','wallet'=>'JazzCash']; @endphp
        <table>
            <thead><tr><th>Method</th><th class="r">Txns</th><th class="r">Amount (Rs)</th></tr></thead>
            <tbody>
                @foreach($byWebPayment as $m => $d)
                <tr><td>{{ $webPmL[$m] ?? ucwords(str_replace('_',' ',$m)) }}</td><td class="r">{{ $d['count'] }}</td><td class="r">{{ number_format($d['total'], 0) }}</td></tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>

{{-- Cashier breakdown on its own row if payments already used both columns --}}
@if($byCashier->count())
<div class="section-title">By Cashier / Shift</div>
<table>
    <thead><tr><th>Cashier</th><th class="r">Orders</th><th class="r">Total (Rs)</th><th class="r">Avg Order</th></tr></thead>
    <tbody>
        @foreach($byCashier as $row)
        <tr>
            <td>{{ $row['name'] }}</td>
            <td class="r">{{ $row['count'] }}</td>
            <td class="r">{{ number_format($row['total'], 0) }}</td>
            <td class="r">{{ $row['count'] ? number_format($row['total']/$row['count'], 0) : 0 }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

@endif

{{-- ══ PRODUCTS + EXPENSES (side by side) ══ --}}
<div class="two-col">
    @if($productSales->count())
    <div class="col-l">
        <div class="section-title">Product Sales</div>
        <table>
            <thead><tr><th>Product / Variant</th><th class="r">Qty</th><th class="r">Revenue (Rs)</th></tr></thead>
            <tbody>
                @php $prodTotal=0; $prodQty=0; @endphp
                @foreach($productSales as $p)
                @php $prodTotal+=$p['total']; $prodQty+=$p['qty']; @endphp
                <tr>
                    <td>{{ $p['name'] }}{{ $p['variant'] ? ' ('.$p['variant'].')' : '' }}</td>
                    <td class="r">{{ $p['qty'] }}</td>
                    <td class="r">{{ number_format($p['total'], 0) }}</td>
                </tr>
                @endforeach
                <tr class="tot"><td>TOTAL</td><td class="r">{{ $prodQty }}</td><td class="r">{{ number_format($prodTotal, 0) }}</td></tr>
            </tbody>
        </table>
    </div>
    @endif

    <div class="{{ $productSales->count() ? 'col-r' : 'col-l' }}">
        <div class="section-title">Expenses</div>
        @if($expenses->count())
        <table>
            <thead><tr><th>Description</th><th>Cat.</th><th class="r">Amount (Rs)</th></tr></thead>
            <tbody>
                @foreach($expenses as $exp)
                <tr>
                    <td>{{ $exp->description }}</td>
                    <td>{{ $exp->category }}</td>
                    <td class="r">{{ number_format($exp->amount, 0) }}</td>
                </tr>
                @endforeach
                <tr class="tot"><td colspan="2">TOTAL EXPENSES</td><td class="r">{{ number_format($totalExpenses, 0) }}</td></tr>
            </tbody>
        </table>
        @else
        <p style="color:#aaa;font-size:8px;padding:6px 0;">No expenses for this period.</p>
        @endif
    </div>
</div>

{{-- ══ ALL ORDERS LIST ══ --}}
@if($orders->count())
<div class="section-title">Order Details</div>
<table>
    <thead>
        <tr>
            <th>Order #</th>
            <th>Source</th>
            <th>Customer</th>
            <th>Type</th>
            <th>Payment</th>
            <th class="r">Total (Rs)</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @php $typeLabels=['pickup'=>'Pickup','booking'=>'Delivery','gift'=>'Gift','online'=>'Website']; @endphp
        @foreach($orders as $o)
        @php
            $isWeb     = $o->source !== 'pos';
            $payMethod = $isWeb
                ? ($o->transaction ? ($o->transaction->mode === 'bank_transfer' ? 'Bank Transfer' : 'JazzCash') : 'N/A')
                : (isset($o->posPayment) ? ucwords(str_replace('_',' ',$o->posPayment->method ?? '')) : 'N/A');
            $orderType = $isWeb ? 'Website' : ($typeLabels[$o->type ?? ''] ?? ucfirst($o->type ?? 'POS'));
        @endphp
        <tr>
            <td style="font-weight:700;">{{ $o->order_number }}</td>
            <td>
                @if($isWeb)
                    <span class="badge b-website">🌐 WEBSITE</span>
                @else
                    <span class="badge b-pos">🖥 POS</span>
                @endif
            </td>
            <td>{{ $o->name ?: ($o->user?->name ?? '—') }}</td>
            <td>{{ $orderType }}</td>
            <td>{{ $payMethod }}</td>
            <td class="r" style="font-weight:700;">{{ number_format($o->total, 0) }}</td>
            <td>{{ ucfirst($o->status) }}</td>
        </tr>
        @endforeach
        <tr class="tot">
            <td colspan="5">TOTAL</td>
            <td class="r">{{ number_format($netSales, 0) }}</td>
            <td>{{ $totalOrders }} orders</td>
        </tr>
    </tbody>
</table>
@endif

{{-- ══ NET TOTAL BOX ══ --}}
<div class="net-box">
    <div class="net-box-l">
        <div class="lbl">TOTAL SALE</div>
        <div class="val">Rs {{ number_format($netSales, 2) }}</div>
        <div class="tiny">{{ $totalOrders }} order(s) &nbsp;·&nbsp; {{ $from->format('d M Y') }}@if($from->toDateString()!==$to->toDateString()) – {{ $to->format('d M Y') }}@endif</div>
    </div>
    <div class="net-box-r">
        <div class="lbl">Less: Expenses</div>
        <div class="exp">– Rs {{ number_format($totalExpenses, 2) }}</div>
        <div class="divider">
            <div class="lbl">NET AMOUNT</div>
            <div class="net">Rs {{ number_format($netSales - $totalExpenses, 2) }}</div>
        </div>
    </div>
</div>

</div>
</body>
</html>

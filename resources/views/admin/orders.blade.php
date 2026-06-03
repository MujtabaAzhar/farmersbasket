@extends('layouts.admin')

@section('content')
    <div class="main-content-inner">
        <div class="main-content-wrap">

            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Orders</h3>
                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                    <li><a href="{{ route('admin.index') }}">
                            <div class="text-tiny">Dashboard</div>
                        </a></li>
                    <li><i class="icon-chevron-right"></i></li>
                    <li>
                        <div class="text-tiny">Orders</div>
                    </li>
                </ul>
            </div>

            @if (session('status'))
                <div class="alert alert-success mb-4">{{ session('status') }}</div>
            @endif

            {{-- Bulk action bar (hidden until rows are selected) --}}


            {{-- ── Filter Panel ───────────────────────────────────────────── --}}
            <div class="wg-box mb-3">
                <form method="GET" action="{{ route('admin.orders') }}" id="filter-form">
                    <div class="row g-2 align-items-end">
                        <div class="col-6 col-md-2">
                            <label class="text-tiny fw-600 mb-1">Order ID</label>
                            <input type="text" name="order_id" class="form-control form-control-sm" placeholder="FB-1001"
                                value="{{ request('order_id') }}">
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="text-tiny fw-600 mb-1">Phone</label>
                            <input type="text" name="phone" class="form-control form-control-sm"
                                placeholder="03XXXXXXXXX" value="{{ request('phone') }}">
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="text-tiny fw-600 mb-1">Payment Method</label>
                            <select name="payment_method" class="form-select form-select-sm">
                                <option value="">All</option>
                                <optgroup label="Method">
                                    <option value="cash"            {{ request('payment_method')==='cash'            ? 'selected':'' }}>Cash</option>
                                    <option value="COD"             {{ request('payment_method')==='COD'             ? 'selected':'' }}>COD</option>
                                </optgroup>
                                <optgroup label="Online Platform">
                                    <option value="JazzCash"        {{ request('payment_method')==='JazzCash'        ? 'selected':'' }}>JazzCash</option>
                                    <option value="EasyPaisa"       {{ request('payment_method')==='EasyPaisa'       ? 'selected':'' }}>EasyPaisa</option>
                                    <option value="Meezan Bank"     {{ request('payment_method')==='Meezan Bank'     ? 'selected':'' }}>Meezan Bank</option>
                                    <option value="HBL Bank"        {{ request('payment_method')==='HBL Bank'        ? 'selected':'' }}>HBL Bank</option>
                                    <option value="Alfalah Bank"    {{ request('payment_method')==='Alfalah Bank'    ? 'selected':'' }}>Alfalah Bank</option>
                                </optgroup>
                            </select>
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="text-tiny fw-600 mb-1">Courier</label>
                            <input type="text" name="courier" class="form-control form-control-sm"
                                placeholder="Courier name" value="{{ request('courier') }}">
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="text-tiny fw-600 mb-1">Status</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">All</option>
                                @foreach (['ordered' => 'Pending', 'confirmed' => 'Confirmed', 'packed' => 'Packed', 'shipped' => 'Shipped', 'delivered' => 'Delivered', 'canceled' => 'Canceled', 'returned' => 'Returned'] as $val => $lbl)
                                    <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>
                                        {{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="text-tiny fw-600 mb-1">Date From</label>
                            <input type="date" name="date_from" class="form-control form-control-sm"
                                value="{{ request('date_from') }}">
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="text-tiny fw-600 mb-1">Date To</label>
                            <input type="date" name="date_to" class="form-control form-control-sm"
                                value="{{ request('date_to') }}">
                        </div>
                        <div class="col-6 col-md-2 d-flex gap-2">
                            <button type="submit" class="tf-button style-1 w-100">
                                <i class="icon-search"></i> Filter
                            </button>
                            <a href="{{ route('admin.orders') }}" class="tf-button w-100 text-center"
                                style="background:#eee;color:#333;">Reset</a>
                        </div>
                    </div>
                    @if (request()->hasAny(['order_id', 'phone', 'payment_method', 'courier', 'status', 'date_from', 'date_to']))
                        <div class="mt-2 text-tiny text-muted">
                            Showing filtered results — {{ $orders->total() }} order(s) found.
                        </div>
                    @endif
                </form>
            </div>

            <div class="wg-box">
                <div class="wg-table table-all-user">
                    <div id="bulk-bar" style="display:none;"
                        class="wg-box mb-3 d-flex align-items-center gap-3 flex-wrap row">
                        <span id="bulk-count" class="fw-600 fs-14 text-muted">0 selected</span>
                        <form id="bulk-form" action="{{ route('admin.orders.bulk.status') }}" method="POST"
                            class="d-flex align-items-center gap-2">
                            @csrf
                            <div id="bulk-ids"></div>
                            <div class="select" style="min-width:160px;">
                                <select name="order_status" required>
                                    <option value="">— Set Status —</option>
                                    @foreach (['ordered' => 'Pending', 'confirmed' => 'Confirmed', 'packed' => 'Packed', 'shipped' => 'Shipped', 'delivered' => 'Delivered', 'canceled' => 'Canceled', 'returned' => 'Returned'] as $val => $label)
                                        <option value="{{ $val }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="tf-button style-1"
                                onclick="return confirmBulk(this.form)">Apply</button>
                        </form>
                        {{-- <button type="button" class="tf-button" style="background:#eee;color:#333;"
                            onclick="clearSelection()">✕ Clear</button> --}}
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th style="width:42px;" class="text-center">
                                        <input type="checkbox" id="select-all" title="Select all"
                                            style="width:16px;height:16px;cursor:pointer;">
                                    </th>
                                    <th style="width:100px">Order No</th>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">Phone</th>
                                    <th class="text-center">Payment</th>
                                    <th class="text-center">Courier</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Order Date</th>
                                    <th class="text-center">Items</th>
                                    <th class="text-center">Delivered On</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    @php
                                        $sc = [
                                            'ordered' => 'warning',
                                            'confirmed' => 'info',
                                            'packed' => 'secondary',
                                            'shipped' => 'primary',
                                            'delivered' => 'success',
                                            'canceled' => 'danger',
                                            'returned' => 'dark',
                                        ];
                                        $pm       = $order->posPayment?->method ?? $order->transaction?->mode ?? null;
                                        $platform = $order->posPayment?->online_platform ?? null;
                                        $pmLabel  = $pm ? ucwords(str_replace('_', ' ', $pm)) : '—';
                                        if ($platform) $pmLabel = $platform;
                                    @endphp
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" class="row-check" value="{{ $order->id }}"
                                                style="width:16px;height:16px;cursor:pointer;">
                                        </td>
                                        <td class="fw-600">{{ $order->order_number }}</td>
                                        <td class="text-center">{{ $order->name ?: '—' }}</td>
                                        <td class="text-center">{{ $order->phone ?: '—' }}</td>
                                        <td class="text-center">
                                            <span
                                                class="badge bg-{{ $pm === 'cash' ? 'success' : ($pm ? 'info' : 'secondary') }}">
                                                {{ $pmLabel }}
                                            </span>
                                        </td>
                                        <td class="text-center">{{ $order->courier_name ?: '—' }}</td>
                                        <td class="text-center fw-600">Rs {{ number_format($order->total, 2) }}</td>
                                        <td class="text-center">
                                            <span
                                                class="badge bg-{{ $sc[$order->status] ?? 'secondary' }}">{{ ucfirst($order->status) }}</span>
                                        </td>
                                        <td class="text-center">{{ $order->created_at->format('d M Y, H:i') }}</td>
                                        <td class="text-center">{{ $order->orderItems->count() }}</td>
                                        <td class="text-center">
                                            {{ $order->delivered_date ? \Carbon\Carbon::parse($order->delivered_date)->format('d M Y') : '—' }}
                                        </td>
                                        <td class="text-center" style="white-space:nowrap;">
                                            @php
                                                $gift = $order->giftOrder;
                                                $isPosRow = $order->source === 'pos';
                                                $cashierName = $isPosRow ? ($order->cashier?->name ?? auth()->user()->name) : auth()->user()->name;
                                                if ($isPosRow && $order->posPayment) {
                                                    $payMethod = strtoupper(str_replace('_',' ',$order->posPayment->method));
                                                    $payDetail = $order->posPayment->online_platform ? ucfirst($order->posPayment->online_platform) : null;
                                                    $cashRcvd  = $order->posPayment->cash_received ?? null;
                                                    $chgGiven  = $order->posPayment->change_given  ?? null;
                                                } elseif ($order->transaction) {
                                                    $payMethod = strtoupper($order->transaction->mode ?? 'N/A');
                                                    $payDetail = null; $cashRcvd = null; $chgGiven = null;
                                                } else {
                                                    $payMethod = 'N/A'; $payDetail = null; $cashRcvd = null; $chgGiven = null;
                                                }
                                                $toAddr = $gift
                                                    ? ['name'=>$gift->receiver_name,'phone'=>$gift->receiver_phone,'address'=>$gift->receiver_address??'','city'=>$gift->receiver_city??'']
                                                    : ['name'=>$order->name??'','phone'=>$order->phone??'','address'=>$order->address??'','city'=>$order->city??''];
                                                $fromAddr = $gift
                                                    ? ['name'=>$gift->sender_name?:"FARMER'S BASKET",'phone'=>$gift->sender_phone?:'03-111-222-384','address'=>$gift->sender_address??'','city'=>$gift->sender_city??'']
                                                    : ['name'=>$order->branch?->name?:"FARMER'S BASKET",'address'=>$order->branch?->address??'','phone'=>'03-111-222-384','city'=>$order->branch?->city?:'Lahore'];
                                                $printItems = $order->orderItems->map(fn($i)=>[
                                                    'name'    => $i->product?->name ?? 'Product #'.$i->product_id,
                                                    'brand'   => $i->product?->brand?->name ?? '',
                                                    'variant' => $i->variant_label ?? '',
                                                    'qty'     => $i->quantity,
                                                    'price'   => $i->price,
                                                    'total'   => $i->price * $i->quantity,
                                                ])->values()->all();
                                                $stickerJson = json_encode([
                                                    'orderNumber'=>$order->order_number,
                                                    'createdAt'=>$order->created_at->format('n/j/y, g:i A'),
                                                    'cashier'=>$cashierName,'isGift'=>(bool)$gift,
                                                    'to'=>$toAddr,'from'=>$fromAddr,
                                                    'message'=>$gift?->gift_message??'',
                                                    'items'=>array_map(fn($i)=>['name'=>$i['name'],'brand'=>$i['brand'],'variant'=>$i['variant'],'qty'=>$i['qty']],$printItems),
                                                ]);
                                                $receiptJson = json_encode([
                                                    'orderNumber'=>$order->order_number,
                                                    'createdAt'=>$order->created_at->format('d/m/Y H:i'),
                                                    'cashier'=>$cashierName,
                                                    'branch'=>$order->branch?->name?:"FARMER'S BASKET",
                                                    'paymentMethod'=>$payMethod,'paymentDetail'=>$payDetail,
                                                    'cashReceived'=>$cashRcvd,'changeGiven'=>$chgGiven,
                                                    'to'=>$toAddr,'from'=>$fromAddr,'items'=>$printItems,
                                                    'totalQty'=>$order->orderItems->sum('quantity'),
                                                    'subtotal'=>$order->subtotal,'tax'=>$order->tax??0,
                                                    'shipping'=>$order->shipping??0,'discount'=>$order->discount??0,
                                                    'total'=>$order->total,
                                                    'courierName'=>$order->courier_name??'',
                                                    'orderNote'=>$order->order_note??'',
                                                    'logoUrl'=>asset('images/logo/logo.png'),
                                                ]);
                                            @endphp
                                            <a href="{{ route('admin.order.details', ['order_id' => $order->id]) }}" title="View Details">
                                                <div class="list-icon-function view-icon d-inline-flex">
                                                    <div class="item eye"><i class="icon-eye"></i></div>
                                                </div>
                                            </a>
                                            <a href="{{ route('admin.order.track', ['order_id' => $order->id]) }}" title="Track Order">
                                               <div class="list-icon-function view-icon d-inline-flex ms-1">
                                                    <div class="item eye"><i class="icon-map"></i></div>
                                                </div>
                                            </a>
                                            <button type="button" title="Print Receipt"
                                                onclick='triggerReceipt({{ $receiptJson }})'
                                                style="background:none;border:none;padding:0;cursor:pointer;">
                                                <div class="list-icon-function view-icon d-inline-flex ms-1">
                                                    <div class="item eye" style="background:#e8f5e9;color:#2e7d32;"><i class="icon-printer"></i></div>
                                                </div>
                                            </button>
                                            <button type="button" title="Print Sticker"
                                                onclick='triggerSticker({{ $stickerJson }})'
                                                style="background:none;border:none;padding:0;cursor:pointer;">
                                                <div class="list-icon-function view-icon d-inline-flex ms-1">
                                                    <div class="item eye" style="background:#fff3e0;color:#e65100;"><i class="icon-package"></i></div>
                                                </div>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center py-4 text-muted">No orders found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="divider"></div>
                <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                    {{ $orders->links('pagination::bootstrap-5') }}
                </div>
            </div>

        </div>
    </div>

    <script>
    // ── Print helpers ─────────────────────────────────────────────────────────
    function escH(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

    function triggerReceipt(data) {
        var d = data;
        function fmt(n){ return 'Rs '+parseFloat(n||0).toLocaleString('en-PK',{minimumFractionDigits:2,maximumFractionDigits:2}); }
        function buildSlip(copyLabel) {
            var addrClass = (d.to.address && d.to.address.length > 40) ? 'r-sm' : 'r-det';
            var header = '<div class="r-hdr">'
                +'<div class="r-hdr-left"><img src="'+escH(d.logoUrl)+'" class="r-logo" alt="Logo">'
                +'<div class="r-hdr-meta">'
                +'<div class="r-meta-line">Date: '+escH(d.createdAt)+' &nbsp;|&nbsp; USER: '+escH(d.cashier)+' &nbsp;|&nbsp; <strong>'+escH(copyLabel)+'</strong></div>'
                +'<div class="r-meta-line">Payment Method : <strong>'+escH(d.paymentMethod)+(d.paymentDetail?' ('+escH(d.paymentDetail)+')':'')+'</strong></div>'
                +'</div></div>'
                +'<div class="r-hdr-right">'
                +'<div class="r-banner">'+escH(d.courierName||'COUNTER SALE')+'</div>'
                +'<div class="r-inv-block">Invoice No:<br><strong>'+escH(d.orderNumber)+' / '+d.totalQty+'</strong></div>'
                +'</div></div>';
            var tofrom = '<div class="r-tofrom">'
                +'<div class="r-to-col"><div class="r-col-hdr">TO</div>'
                +'<div class="r-name">'+escH(d.to.name)+'</div>'
                +(d.to.address?'<div class="'+addrClass+'">Delivery Address: '+escH(d.to.address)+'</div>':'')
                +'<div class="r-det">Contact: '+escH(d.to.phone)+'</div>'
                +(d.to.city?'<div class="r-city">City: '+escH(d.to.city).toUpperCase()+'</div>':'')
                +'</div>'
                +'<div class="r-from-col"><div class="r-col-hdr">FROM</div>'
                +'<div class="r-name">'+escH(d.from.name)+'</div>'
                +'<div class="r-det">Address:</div>'
                +'<div class="r-det">Contact: '+escH(d.from.phone)+'</div>'
                +(d.from.city?'<div class="r-city">City: '+escH(d.from.city)+'</div>':'')
                +'</div></div>';
            var itemLines='';
            d.items.forEach(function(item){
                var label=escH(item.name)+(item.variant?' ('+escH(item.variant)+')':'');
                var brand=item.brand?'<span class="r-brand">'+escH(item.brand)+'</span>':'';
                itemLines+='<tr><td class="r-dt-lbl">'+label+brand+'</td><td class="r-dt-mid">'+item.qty+' &times; '+fmt(item.price)+'</td><td class="r-dt-val">'+fmt(item.total)+'</td></tr>';
            });
            var delivRow=d.shipping>0?'<tr><td class="r-dt-lbl">Delivery By: '+(d.courierName?'<span class="r-courier-banner">'+escH(d.courierName)+'</span>':'')+'</td><td class="r-dt-mid">'+d.totalQty+' &times; '+fmt(d.shipping/d.totalQty)+'</td><td class="r-dt-val">'+fmt(d.shipping)+'</td></tr>':'';
            var discRow=d.discount>0?'<tr><td class="r-dt-lbl">Disc Amt (%):</td><td class="r-dt-mid"></td><td class="r-dt-val">- '+fmt(d.discount)+'</td></tr>':'<tr><td class="r-dt-lbl">Disc Amt (%):</td><td class="r-dt-mid"></td><td class="r-dt-val">'+fmt(0)+'</td></tr>';
            var details='<div class="r-details"><table class="r-dt-table"><tbody>'
                +'<tr><td class="r-dt-branch" colspan="3">'+escH(d.branch)+'</td></tr>'
                +'<tr><td class="r-dt-lbl">Total Item(s): '+d.totalQty+'</td><td></td><td></td></tr>'
                +itemLines
                +'<tr class="r-divrow"><td colspan="3"><div class="r-thin-line"></div></td></tr>'
                +'<tr><td class="r-dt-lbl">Sub Total:</td><td></td><td class="r-dt-val">'+fmt(d.subtotal)+'</td></tr>'
                +discRow+delivRow
                +'<tr class="r-divrow"><td colspan="3"><div class="r-thin-line"></div></td></tr>'
                +'<tr><td class="r-dt-grand">Grand Total:</td><td></td><td class="r-dt-grand r-dt-val">'+fmt(d.total)+'</td></tr>'
                +'<tr><td class="r-dt-lbl">Paid Amount:</td><td></td><td class="r-dt-val">'+fmt(d.total)+'</td></tr>'
                +'</tbody></table></div>';
            var footer='<div class="r-footer">For Inquiries &amp; suggestions Please Contact: Help line: <strong>03-111-222-384</strong><br>Thanks For Visiting At Farmer\'s Basket</div>';
            return '<div class="slip">'+header+tofrom+details+footer+'</div>';
        }
        var css=[
            '* { margin:0; padding:0; box-sizing:border-box; }',
            'body { font-family:Arial,Helvetica,sans-serif; font-size:9.5pt; color:#000; background:#fff; width:210mm; }',
            '@page { size:A4 portrait; margin:0; }',
            '.slip { width:210mm; height:148.5mm; padding:4mm 6mm 3mm; display:flex; flex-direction:column; overflow:hidden; }',
            '.cut-line { width:210mm; height:4mm; display:flex; align-items:center; justify-content:center; font-size:8pt; color:#aaa; letter-spacing:2px; border-top:1px dashed #bbb; border-bottom:1px dashed #bbb; }',
            '.r-hdr { display:flex; align-items:stretch; gap:4mm; border-bottom:1.5px solid #000; padding-bottom:2mm; margin-bottom:2mm; }',
            '.r-hdr-left { display:flex; align-items:center; gap:3mm; flex:1; }',
            '.r-logo { height:34px; width:auto; flex-shrink:0; }',
            '.r-hdr-meta { display:flex; flex-direction:column; gap:2px; }',
            '.r-meta-line { font-size:8.5pt; color:#222; }',
            '.r-hdr-right { display:flex; flex-direction:column; align-items:flex-end; justify-content:center; min-width:65mm; }',
            '.r-banner { background:#000; color:#fff; font-size:12pt; font-weight:bold; letter-spacing:1px; padding:4px 8px; text-align:center; width:100%; }',
            '.r-inv-block { font-size:9.5pt; text-align:right; margin-top:2px; line-height:1.4; }',
            '.r-tofrom { display:flex; border:1.5px solid #000; margin-bottom:2mm; }',
            '.r-to-col { flex:1; padding:2mm 3mm; border-right:1.5px solid #000; }',
            '.r-from-col { flex:1; padding:2mm 3mm; }',
            '.r-col-hdr { text-align:center; font-weight:bold; font-size:11pt; letter-spacing:1px; border-bottom:1px solid #000; margin-bottom:1.5mm; padding-bottom:2px; }',
            '.r-name { font-size:11pt; font-weight:bold; line-height:1.3; }',
            '.r-det { font-size:9pt; margin:2px 0; }',
            '.r-sm { font-size:7.5pt; margin:2px 0; }',
            '.r-city { background:#000; color:#fff; text-align:center; font-weight:bold; font-size:10pt; padding:3px 0; margin-top:2mm; letter-spacing:1px; }',
            '.r-details { flex:1; border:1.5px solid #000; padding:1.5mm 2.5mm; margin-bottom:1.5mm; }',
            '.r-dt-table { width:100%; border-collapse:collapse; }',
            '.r-dt-table td { font-size:9pt; padding:2px 3px; vertical-align:middle; }',
            '.r-dt-branch { font-weight:bold; font-size:10pt; padding-bottom:1.5mm !important; }',
            '.r-dt-lbl { width:55%; }',
            '.r-dt-mid { width:25%; text-align:center; color:#333; }',
            '.r-dt-val { width:20%; text-align:right; font-weight:600; }',
            '.r-dt-grand { font-weight:bold; font-size:10pt; }',
            '.r-brand { background:#000; color:#fff; font-weight:bold; font-size:8pt; margin-left:3px; padding:1px 6px; }',
            '.r-thin-line { border-top:1px solid #000; margin:2px 0; }',
            '.r-courier-banner { background:#000; color:#fff; font-weight:bold; padding:1px 6px; font-size:8pt; }',
            '.r-footer { text-align:center; font-size:8pt; border-top:1px solid #000; padding-top:1.5mm; line-height:1.6; margin-top:auto; }',
        ].join('\n');
        var slip1=buildSlip('Customer Slip');
        var cut='<div class="cut-line">&#9988; &mdash;&mdash;&mdash;&mdash;&mdash; cut here &mdash;&mdash;&mdash;&mdash;&mdash; &#9988;</div>';
        var slip2=buildSlip('Counter Copy');
        var win=window.open('','_blank','width=800,height=900,scrollbars=yes,resizable=yes');
        win.document.write('<!DOCTYPE html><html><head><meta charset="utf-8"><title>Receipt &mdash; '+escH(d.orderNumber)+'</title><style>'+css+'</style></head><body>'+slip1+cut+slip2+'</body></html>');
        win.document.close(); win.focus();
        setTimeout(function(){ win.print(); }, 400);
    }

    function triggerSticker(data) {
        var d = data;
        var css=[
            '* { margin:0; padding:0; box-sizing:border-box; }',
            'body { font-family:Arial,Helvetica,sans-serif; color:#000; background:#fff; }',
            '@page { size:4in 4in; margin:0; }',
            '.sticker { width:4in; height:4in; padding:10px 14px 8px; display:flex; flex-direction:column; gap:5px; page-break-after:always; overflow:hidden; }',
            '.sticker:last-child { page-break-after:avoid; }',
            '.s-inv-lg { font-size:14pt; font-weight:bold; margin-top:1px; text-align:center; }',
            '.s-helpline { font-size:9.5pt; font-weight:bold; color:#000; text-align:center; margin-top:1px; padding-top:4px; }',
            '.s-user-row { display:flex; justify-content:space-between; font-size:9.5pt; font-weight:600; }',
            '.s-combined-box { border:2px solid #000; padding:6px 10px 5px; }',
            '.s-inner-divider { border-top:1px dashed #000; margin:6px 0; }',
            '.s-box-title { text-align:center; font-weight:bold; font-size:13pt; letter-spacing:2px; margin-bottom:3px; }',
            '.s-to-name { font-size:13pt; font-weight:bold; line-height:1.15; }',
            '.s-from-name { font-size:13pt; font-weight:700; }',
            '.s-detail { font-size:13pt; margin:2px 0; }',
            '.s-detail-sm { font-size:9pt; margin:2px 0; }',
            '.s-city-bar { background:#000; color:#fff; text-align:center; font-weight:bold; font-size:13pt; padding:3px 0; margin-top:5px; letter-spacing:1px; }',
        ].join('\n');
        var total=d.items.reduce(function(s,i){return s+i.qty;},0);
        var html='', idx=0;
        d.items.forEach(function(item){
            for(var i=0;i<item.qty;i++){
                var addrClass=(d.to.address&&d.to.address.length>45)?'s-detail-sm':'s-detail';
                var s='<div class="sticker">';
                s+='<div class="s-inv-lg">Invoice No: '+escH(d.orderNumber)+' / '+total+'</div>';
                s+='<div class="s-user-row"><span>USER: '+escH(d.cashier)+'</span>'+(item.brand?'<span>Brand: <strong>'+escH(item.brand)+'</strong></span>':'')+'</div>';
                s+='<div class="s-combined-box">';
                s+='<div class="s-box-title">TO</div>';
                s+='<div class="s-to-name">'+escH(d.to.name)+'</div>';
                if(d.to.address) s+='<div class="'+addrClass+'">Delivery Address: '+escH(d.to.address)+'</div>';
                s+='<div class="s-detail">Contact: '+escH(d.to.phone)+'</div>';
                if(d.to.city) s+='<div class="s-city-bar">City: '+escH(d.to.city).toUpperCase()+'</div>';
                s+='<div class="s-inner-divider"></div>';
                s+='<div class="s-box-title">FROM</div>';
                s+='<div class="s-from-name">'+escH(d.from.name)+'</div>';
                s+='<div class="s-detail">Contact: '+escH(d.from.phone)+'</div>';
                if(d.from.city) s+='<div class="s-city-bar">City: '+escH(d.from.city)+'</div>';
                s+='</div>';
                s+='<div class="s-helpline">In case of any query please contact our customer Helpline 03-111-222-384</div>';
                s+='</div>';
                html+=s; idx++;
            }
        });
        if(!html){alert('No items to print stickers for.');return;}
        var win=window.open('','_blank','width=600,height=600,scrollbars=yes,resizable=yes');
        win.document.write('<!DOCTYPE html><html><head><meta charset="utf-8"><title>Stickers &mdash; '+escH(d.orderNumber)+'</title><style>'+css+'</style></head><body>'+html+'</body></html>');
        win.document.close(); win.focus();
        setTimeout(function(){win.print();},400);
    }
    // ─────────────────────────────────────────────────────────────────────────

        var selected = new Set();

        // Select-all toggle
        document.getElementById('select-all').addEventListener('change', function() {
            document.querySelectorAll('.row-check').forEach(function(cb) {
                cb.checked = this.checked;
                if (this.checked) selected.add(cb.value);
                else selected.delete(cb.value);
            }, this);
            updateBar();
        });

        // Individual row checkbox
        document.querySelectorAll('.row-check').forEach(function(cb) {
            cb.addEventListener('change', function() {
                if (this.checked) selected.add(this.value);
                else selected.delete(this.value);

                // Sync select-all header state
                var all = document.querySelectorAll('.row-check').length;
                var chk = document.querySelectorAll('.row-check:checked').length;
                var sa = document.getElementById('select-all');
                sa.checked = chk === all;
                sa.indeterminate = chk > 0 && chk < all;

                updateBar();
            });
        });

        function updateBar() {
            var bar = document.getElementById('bulk-bar');
            var count = document.getElementById('bulk-count');
            var ids = document.getElementById('bulk-ids');

            if (selected.size > 0) {
                bar.style.display = '';
                count.textContent = selected.size + ' order' + (selected.size > 1 ? 's' : '') + ' selected';
                ids.innerHTML = '';
                selected.forEach(function(id) {
                    var inp = document.createElement('input');
                    inp.type = 'hidden';
                    inp.name = 'order_ids[]';
                    inp.value = id;
                    ids.appendChild(inp);
                });
            } else {
                bar.style.display = 'none';
            }
        }

        function clearSelection() {
            selected.clear();
            document.querySelectorAll('.row-check').forEach(function(cb) {
                cb.checked = false;
            });
            document.getElementById('select-all').checked = false;
            document.getElementById('select-all').indeterminate = false;
            updateBar();
        }

        function confirmBulk(form) {
            var status = form.querySelector('[name=order_status]').value;
            if (!status) {
                alert('Please select a status first.');
                return false;
            }
            return confirm('Update ' + selected.size + ' order(s) to "' + status + '"?');
        }

    </script>
@endsection

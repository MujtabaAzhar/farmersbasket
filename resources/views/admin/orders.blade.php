@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">

        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Orders</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Orders</div></li>
            </ul>
        </div>

        @if(session('status'))
            <div class="alert alert-success mb-4">{{ session('status') }}</div>
        @endif

        {{-- Bulk action bar (hidden until rows are selected) --}}
        <div id="bulk-bar" style="display:none;" class="wg-box mb-3 d-flex align-items-center gap-3 flex-wrap">
            <span id="bulk-count" class="fw-600 fs-14 text-muted">0 selected</span>
            <form id="bulk-form" action="{{ route('admin.orders.bulk.status') }}" method="POST" class="d-flex align-items-center gap-2">
                @csrf
                <div id="bulk-ids"></div>
                <div class="select" style="min-width:160px;">
                    <select name="order_status" required>
                        <option value="">— Set Status —</option>
                        @foreach(['ordered'=>'Pending','confirmed'=>'Confirmed','packed'=>'Packed','shipped'=>'Shipped','delivered'=>'Delivered','canceled'=>'Canceled','returned'=>'Returned'] as $val => $label)
                            <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="tf-button style-1" onclick="return confirmBulk(this.form)">Apply</button>
            </form>
            <button type="button" class="tf-button" style="background:#eee;color:#333;" onclick="clearSelection()">✕ Clear</button>
        </div>

        <div class="wg-box">
            <div class="flex items-center justify-between gap10 flex-wrap mb-3">
                <div class="wg-filter flex-grow">
                    <form class="form-search">
                        <fieldset class="name">
                            <input type="text" placeholder="Search here..." name="name" tabindex="2" value="" aria-required="true">
                        </fieldset>
                        <div class="button-submit">
                            <button type="submit"><i class="icon-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="wg-table table-all-user">
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
                                <th class="text-center">Subtotal</th>
                                <th class="text-center">Tax</th>
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
                                $sc = ['ordered'=>'warning','confirmed'=>'info','packed'=>'secondary','shipped'=>'primary','delivered'=>'success','canceled'=>'danger','returned'=>'dark'];
                            @endphp
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" class="row-check" value="{{ $order->id }}"
                                           style="width:16px;height:16px;cursor:pointer;">
                                </td>
                                <td class="fw-600">{{ $order->order_number }}</td>
                                <td class="text-center">{{ $order->name ?: '—' }}</td>
                                <td class="text-center">{{ $order->phone }}</td>
                                <td class="text-center">Rs {{ number_format($order->subtotal, 2) }}</td>
                                <td class="text-center">Rs {{ number_format($order->tax, 2) }}</td>
                                <td class="text-center fw-600">Rs {{ number_format($order->total, 2) }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $sc[$order->status] ?? 'secondary' }}">{{ ucfirst($order->status) }}</span>
                                </td>
                                <td class="text-center">{{ $order->created_at->format('d M Y, H:i') }}</td>
                                <td class="text-center">{{ $order->orderItems->count() }}</td>
                                <td class="text-center">{{ $order->delivered_date ? \Carbon\Carbon::parse($order->delivered_date)->format('d M Y') : '—' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.order.details', ['order_id' => $order->id]) }}">
                                        <div class="list-icon-function view-icon">
                                            <div class="item eye"><i class="icon-eye"></i></div>
                                        </div>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="12" class="text-center py-4 text-muted">No orders found.</td></tr>
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
var selected = new Set();

// Select-all toggle
document.getElementById('select-all').addEventListener('change', function () {
    document.querySelectorAll('.row-check').forEach(function (cb) {
        cb.checked = this.checked;
        if (this.checked) selected.add(cb.value);
        else selected.delete(cb.value);
    }, this);
    updateBar();
});

// Individual row checkbox
document.querySelectorAll('.row-check').forEach(function (cb) {
    cb.addEventListener('change', function () {
        if (this.checked) selected.add(this.value);
        else selected.delete(this.value);

        // Sync select-all header state
        var all  = document.querySelectorAll('.row-check').length;
        var chk  = document.querySelectorAll('.row-check:checked').length;
        var sa   = document.getElementById('select-all');
        sa.checked       = chk === all;
        sa.indeterminate = chk > 0 && chk < all;

        updateBar();
    });
});

function updateBar() {
    var bar   = document.getElementById('bulk-bar');
    var count = document.getElementById('bulk-count');
    var ids   = document.getElementById('bulk-ids');

    if (selected.size > 0) {
        bar.style.display = '';
        count.textContent = selected.size + ' order' + (selected.size > 1 ? 's' : '') + ' selected';
        ids.innerHTML = '';
        selected.forEach(function (id) {
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
    document.querySelectorAll('.row-check').forEach(function (cb) { cb.checked = false; });
    document.getElementById('select-all').checked = false;
    document.getElementById('select-all').indeterminate = false;
    updateBar();
}

function confirmBulk(form) {
    var status = form.querySelector('[name=order_status]').value;
    if (!status) { alert('Please select a status first.'); return false; }
    return confirm('Update ' + selected.size + ' order(s) to "' + status + '"?');
}
</script>
@endsection

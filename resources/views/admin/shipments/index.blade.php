@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">

        {{-- Page header --}}
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Shipments Overview</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Shipments</div></li>
            </ul>
        </div>

        @if(session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif

      

        {{-- ── Courier Cards ─────────────────────────────────────────────────── --}}
        @if($allCouriers->isNotEmpty())
        <div class="wg-box mb-4">
            <div class="row g-3">
                @foreach($allCouriers as $c)
                @php
                    $cMonth = $thisMonth[$c->courier_name]->total_month ?? 0;
                    $cLast  = $lastMonth[$c->courier_name]->total_last  ?? 0;
                    $diff   = $cMonth - $cLast;
                @endphp
                <div class="col-6 col-md-3">
                    <a href="{{ route('admin.shipments.index', ['courier' => $c->courier_name]) }}"
                       style="text-decoration:none;">
                        <div class="wg-box h-100 text-center py-3 px-2"
                             style="border:2px solid {{ request('courier') === $c->courier_name ? '#2ecc71' : '#eee' }};transition:.2s;cursor:pointer;"
                             onmouseover="this.style.borderColor='#2ecc71'"
                             onmouseout="this.style.borderColor='{{ request('courier') === $c->courier_name ? '#2ecc71' : '#eee' }}'">
                            <div style="font-size:22px;font-weight:800;color:#1a1f2e;">
                                {{ $c->courier_name }}
                            </div>
                            <div class="mt-2 mb-1" style="font-size:26px;font-weight:800;color:#3498db;">
                                {{ $c->total_all }}
                            </div>
                            <div class="text-muted" style="font-size:11px;">All-time orders</div>
                            <hr class="my-2">
                            <div style="font-size:18px;font-weight:700;color:#2ecc71;">{{ $cMonth }}</div>
                            <div class="text-muted" style="font-size:11px;">This month</div>
                            @if($cLast > 0 || $cMonth > 0)
                            <div class="mt-1" style="font-size:11px;color:{{ $diff >= 0 ? '#2ecc71' : '#e74c3c' }};">
                                {{ $diff >= 0 ? '▲' : '▼' }} {{ abs($diff) }} vs last month
                            </div>
                            @endif
                        </div>
                    </a>
                </div>
                @endforeach
                
            </div>
        </div>
        @endif



  

    </div>
</div>
@endsection
